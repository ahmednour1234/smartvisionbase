<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminUserStoreRequest;
use App\Http\Requests\Api\Admin\AdminUserUpdateRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->get([
            'id',
            'name',
            'email',
            'role',
            'is_active',
            'must_change_password',
            'created_at',
        ]);

        return response()->json(['users' => $users]);
    }

    public function store(AdminUserStoreRequest $request)
    {
        $actor = $request->user();
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => array_key_exists('is_active', $data) ? (int) (bool) $data['is_active'] : 1,
            'must_change_password' => array_key_exists('must_change_password', $data)
                ? (int) (bool) $data['must_change_password']
                : 1,
        ]);

        AuditService::log($actor->id ?? null, 'create', 'user', $user->id, ['email' => $user->email], AuditService::ip($request));

        return response()->json(['user' => $user], 201);
    }

    public function update(AdminUserUpdateRequest $request, int $id)
    {
        $actor = $request->user();
        $data = $request->validated();

        $user = User::findOrFail($id);

        if (!empty($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
        }
        unset($data['password']);

        $user->fill($data);
        $user->save();

        AuditService::log($actor->id ?? null, 'update', 'user', $user->id, ['changed' => array_keys($data)], AuditService::ip($request));

        return response()->json(['user' => $user]);
    }

    public function destroy(Request $request, int $id)
    {
        $actor = $request->user();
        $user = User::findOrFail($id);

        // Prevent self-delete
        if ($actor && (int) $actor->id === (int) $user->id) {
            return response()->json(['message' => 'Cannot delete your own account'], 422);
        }

        $user->delete();

        AuditService::log($actor->id ?? null, 'delete', 'user', $id, [], AuditService::ip($request));

        return response()->json(['message' => 'Deleted']);
    }
}
