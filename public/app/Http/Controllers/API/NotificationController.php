<?php
// app/Http/Controllers/API/NotificationController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // GET /api/notifications?per_page=20&unread=1
    public function index(Request $request)
    {
        [$type, $id] = $this->currentActor();

        $perPage = (int) $request->integer('per_page', 20);
        $q = SystemNotification::query()
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $id)
            ->when($request->boolean('unread'), fn($qq) => $qq->whereNull('read_at'))
            ->when($s = $request->query('q'), fn($qq) =>
                $qq->where(function($w) use ($s){
                    $w->where('title', 'like', "%{$s}%")
                      ->orWhere('body', 'like', "%{$s}%");
                }))
            ->recent();

        $p = $q->paginate($perPage)->appends($request->query());
        return response()->json($p);
    }

    // POST /api/notifications/{id}/read
    public function markRead(int $id)
    {
        [$type, $userId] = $this->currentActor();

        $n = SystemNotification::where('id', $id)
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $userId)
            ->firstOrFail();

        if (!$n->read_at) $n->update(['read_at' => now()]);

        return response()->json(['message' => 'OK', 'read_at' => $n->read_at]);
    }

    // POST /api/notifications/read-all
    public function markAllRead(Request $request)
    {
        [$type, $userId] = $this->currentActor();

        SystemNotification::where('notifiable_type', $type)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'OK']);
    }

    // DELETE /api/notifications/{id}
    public function destroy(int $id)
    {
        [$type, $userId] = $this->currentActor();

        SystemNotification::where('id', $id)
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $userId)
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function currentActor(): array
    {
        foreach (['client','speaker','sponsor','sanctum','web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $u = Auth::guard($guard)->user();
                return [get_class($u), (int)$u->getKey()];
            }
        }
        abort(401, 'Unauthenticated');
    }
}
