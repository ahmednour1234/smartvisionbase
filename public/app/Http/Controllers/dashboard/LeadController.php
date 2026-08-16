<?php
// app/Http/Controllers/Dashboard/LeadController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadComment;
use App\Models\LeadCallLog;
use App\Models\Client;
use App\Models\Qrcode as QrcodeModel;
use App\Exports\LeadsExport;
use App\Services\WatiService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Throwable;

class LeadController extends Controller
{
    /* ===================== Utilities ===================== */

    protected function isLocked(Lead $lead): bool
    {
        return in_array($lead->status, ['accept', 'reject'], true);
    }

    /**
     * نحافظ على “دلالة” assigned_user_id:
     * - لو المفتاح غير موجود في الـ query إطلاقًا => فلترة على المستخدم الحالي.
     * - لو المفتاح موجود لكنه فارغ => لا فلترة (All).
     * - لو له قيمة => فلترة بهذه القيمة.
     */
    protected function applyAssignedUserFilter($builder, Request $request, string $col = 'assigned_user_id')
    {
        if (!$request->has('assigned_user_id')) {
            $builder->where($col, auth()->id());
        } else {
            $val = $request->input('assigned_user_id');
            if ($val !== null && $val !== '') {
                $builder->where($col, (int) $val);
            }
        }
        return $builder;
    }

    protected function applyAssignedUserFilterQB($qb, Request $request, string $alias = 'l')
    {
        return $this->applyAssignedUserFilter($qb, $request, "{$alias}.assigned_user_id");
    }

    /**
     * نعيد نفس الفلاتر “كما استُلمت” مع الحفاظ على المفاتيح ذات القيم الفارغة إذا كانت موجودة فعلاً في الـ query.
     * هذا مهم للـ pagination حتى لا تتحول (All) إلى فلترة على المستخدم الحالي فجأة.
     */
    protected function extractFilters(Request $request): array
    {
        $keys = ['q','status','assigned_user_id','assigned_by','assigned_by_id','from','to'];

        $filters = [];
        foreach ($keys as $k) {
            if ($request->has($k)) {
                // احتفظ بالقيمة حتى لو كانت empty string
                $filters[$k] = $request->query($k);
            }
        }
        return $filters;
    }

    protected function autoMarkLost(): void
    {
        Lead::query()
            ->whereIn('status', ['new', 'follow_up'])
            ->whereNotNull('next_call_at')
            ->where('next_call_at', '<', now())
            ->whereDoesntHave('callLogs', function ($q) {
                $q->whereColumn('lead_call_logs.called_at', '>=', 'leads.next_call_at');
            })
            ->update([
                'status'     => 'lost',
                'updated_at' => now(),
            ]);
    }

    protected function buildFilteredLeadsQB(Request $request, string $alias = 'l')
    {
        $t  = $alias;
        $qb = DB::table("leads as {$t}");

        if ($request->filled('q')) {
            $s = trim((string) $request->q);
            $qb->where(function ($y) use ($s, $t) {
                $y->where("{$t}.name",  'like', "%{$s}%")
                  ->orWhere("{$t}.email", 'like', "%{$s}%")
                  ->orWhere("{$t}.phone", 'like', "%{$s}%")
                  ->orWhere("{$t}.job",   'like', "%{$s}%");
            });
        }

        $qb = $this->applyAssignedUserFilterQB($qb, $request, $t);

        if ($request->filled('status')) {
            $qb->where("{$t}.status", $request->status);
        }

        if (Schema::hasColumn('leads', 'assigned_by')) {
            $assignedByParam = $request->input('assigned_by_id', $request->input('assigned_by'));
            if ($assignedByParam !== null && $assignedByParam !== '') {
                $qb->where("{$t}.assigned_by", (int) $assignedByParam);
            }
        }

        if ($request->filled('from')) {
            $qb->whereDate("{$t}.created_at", '>=', $request->from);
        }
        if ($request->filled('to')) {
            $qb->whereDate("{$t}.created_at", '<=', $request->to);
        }

        return $qb;
    }

    protected function aggregatedUserStats(Request $request)
    {
        $leadsFilteredForAgg = $this->buildFilteredLeadsQB($request, 'lf1');

        $leadAggPerUser = $this->buildFilteredLeadsQB($request, 'lf2')
            ->selectRaw("
                lf2.assigned_user_id as uid,
                SUM(CASE WHEN lf2.status='new' THEN 1 ELSE 0 END)        as cnt_new,
                SUM(CASE WHEN lf2.status='follow_up' THEN 1 ELSE 0 END)  as cnt_follow_up,
                SUM(CASE WHEN lf2.status='accept' THEN 1 ELSE 0 END)     as cnt_accept,
                SUM(CASE WHEN lf2.status='reject' THEN 1 ELSE 0 END)     as cnt_reject,
                SUM(CASE WHEN lf2.status='lost' THEN 1 ELSE 0 END)       as cnt_lost,
                COUNT(*)                                                 as total_leads
            ")
            ->groupBy('lf2.assigned_user_id');

        $leadIdsSub = (clone $leadsFilteredForAgg)->select('lf1.id');

        $commentsAggPerUser = DB::table('lead_comments as lc')
            ->joinSub($leadIdsSub, 'L', fn($j) => $j->on('L.id', '=', 'lc.lead_id'))
            ->selectRaw('lc.user_id as uid, COUNT(*) as c')
            ->groupBy('lc.user_id');

        $callsAggPerUser = DB::table('lead_call_logs as ll')
            ->joinSub($leadIdsSub, 'L2', fn($j) => $j->on('L2.id', '=', 'll.lead_id'))
            ->selectRaw('ll.user_id as uid, COUNT(*) as c')
            ->groupBy('ll.user_id');

        $stats = DB::table('users as u')
            ->when(Schema::hasColumn('users','type'), fn($q)=>$q->where('type','callcenter'))
            ->leftJoinSub($leadAggPerUser, 'LA', fn($j) => $j->on('LA.uid', '=', 'u.id'))
            ->leftJoinSub($commentsAggPerUser, 'LC', fn($j) => $j->on('LC.uid', '=', 'u.id'))
            ->leftJoinSub($callsAggPerUser, 'LL', fn($j) => $j->on('LL.uid', '=', 'u.id'))
            ->when(!$request->has('assigned_user_id'), function ($q) {
                $q->where('u.id', auth()->id());
            })
            ->when($request->has('assigned_user_id') && $request->input('assigned_user_id') !== '', function ($q) use ($request) {
                $q->where('u.id', (int) $request->input('assigned_user_id'));
            })
            ->selectRaw("
                u.id,
                u.name,
                COALESCE(LA.cnt_new, 0)       as cnt_new,
                COALESCE(LA.cnt_follow_up, 0) as cnt_follow_up,
                COALESCE(LA.cnt_accept, 0)    as cnt_accept,
                COALESCE(LA.cnt_reject, 0)    as cnt_reject,
                COALESCE(LA.cnt_lost, 0)      as cnt_lost,
                COALESCE(LA.total_leads, 0)   as total_leads,
                COALESCE(LC.c, 0)             as comments_count,
                COALESCE(LL.c, 0)             as calllogs_count
            ")
            ->orderByDesc('total_leads')
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get();

        return $stats;
    }

    protected function withCreatedBy(array $data): array
    {
        if (Schema::hasColumn('leads', 'created_by')) {
            $data['created_by'] = auth()->id();
        }
        return $data;
    }

    protected function updateLeadQrId(Lead $lead, ?int $qrcodeId): void
    {
        if (!$qrcodeId) return;

        $payload = [];
        if (Schema::hasColumn('leads', 'qrcode_id')) {
            $payload['qrcode_id'] = $qrcodeId;
        }
        if (Schema::hasColumn('leads', 'qecode_id')) {
            $payload['qecode_id'] = $qrcodeId; // دعم العمود المكتوب خطأ
        }

        if (!empty($payload)) {
            $lead->forceFill($payload)->save();
        }
    }

    /* ===================== Index / Show ===================== */

    public function index(Request $request)
    {
        $this->autoMarkLost();

        $q = Lead::query()
            ->with([
                'assignedUser:id,name',
                'comments' => fn($c) => $c->latest()->limit(1),
            ])
            ->when($request->filled('q'), function ($x) use ($request) {
                $s = trim((string) $request->q);
                $x->where(function ($y) use ($s) {
                    $y->where('name',  'like', "%{$s}%")
                      ->orWhere('email','like', "%{$s}%")
                      ->orWhere('phone','like', "%{$s}%")
                      ->orWhere('job',  'like', "%{$s}%");
                });
            });

        $this->applyAssignedUserFilter($q, $request);

        $q->when($request->filled('status'), fn($x) => $x->where('status', $request->status))
          ->when(Schema::hasColumn('leads', 'assigned_by') && ($request->filled('assigned_by') || $request->filled('assigned_by_id')), function ($x) use ($request) {
              $val = $request->input('assigned_by_id', $request->input('assigned_by'));
              $x->where('assigned_by', (int) $val);
          })
          ->when($request->filled('from'), fn($x) => $x->whereDate('updated_at', '>=', $request->from))
          ->when($request->filled('to'),   fn($x) => $x->whereDate('updated_at', '<=', $request->to))
          ->latest();

        // >>> مفتاح الحل: withQueryString يورّث كل مفاتيح الاستعلام كما هي (بما فيها الفارغة إن كانت موجودة)
        $leads     = $q->paginate(500)->withQueryString();
        $userStats = $this->aggregatedUserStats($request);

        return view('content.customer.index', [
            'leads'     => $leads,
            'filters'   => $this->extractFilters($request),
            'statuses'  => ['new','follow_up','accept','reject','lost'],
            'userStats' => $userStats,
        ]);
    }

    public function show(Lead $lead)
    {
        $this->autoMarkLost();

        $lead->load([
            'assignedUser:id,name',
            'comments'       => fn($q) => $q->latest(),
            'comments.user:id,name',
            'callLogs'       => fn($q) => $q->orderByDesc('called_at'),
            'callLogs.user:id,name',
        ]);

        $lastComment = $lead->comments->first();
        $lastCall    = $lead->callLogs->first();

        $lastActionType = null;
        $lastActionAt   = null;

        if ($lastComment && $lastCall) {
            $lastActionType = Carbon::parse($lastComment->created_at)->gt(Carbon::parse($lastCall->called_at))
                ? 'comment' : 'call';
            $lastActionAt   = $lastActionType === 'comment'
                ? $lastComment->created_at
                : $lastCall->called_at;
        } elseif ($lastComment) {
            $lastActionType = 'comment';
            $lastActionAt   = $lastComment->created_at;
        } elseif ($lastCall) {
            $lastActionType = 'call';
            $lastActionAt   = $lastCall->called_at;
        }

        return view('content.customer.show', compact('lead','lastActionType','lastActionAt'));
    }

    /* ===================== CRUD ===================== */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:190',
            'email'        => 'nullable|email|max:190',
            'phone'        => 'nullable|string|max:50',
            'job'          => 'nullable|string|max:190',
            'status'       => 'nullable|in:new,follow_up,accept,reject,lost',
            'source'       => 'nullable|string|max:190',
            'notes'        => 'nullable|string',
            'next_call_at' => 'nullable|date',
        ]);

        $data['assigned_user_id'] = auth()->id();
        $data = $this->withCreatedBy($data);

        if (Schema::hasColumn('leads', 'assigned_by')) {
            $data['assigned_by'] = auth()->id();
        }
        if (Schema::hasColumn('leads', 'assigned_at')) {
            $data['assigned_at'] = now();
        }

        Lead::create($data);
        return back()->with('success', 'Lead created successfully.');
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:190',
            'email'        => 'nullable|email|max:190',
            'phone'        => 'nullable|string|max:50',
            'job'          => 'nullable|string|max:190',
            'status'       => 'nullable|in:new,follow_up,accept,reject,lost',
            'source'       => 'nullable|string|max:190',
            'notes'        => 'nullable|string',
            'next_call_at' => 'nullable|date',
        ]);

        if ($this->isLocked($lead) && isset($data['status']) && $data['status'] !== $lead->status) {
            unset($data['status']);
            return back()->with('error', 'Status is locked (accept/reject).');
        }

        $lead->update($data);
        return back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return back()->with('success', 'Lead deleted successfully.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $request->validate(['assigned_user_id' => 'required|integer|exists:users,id']);

        $payload = ['assigned_user_id' => (int) $request->assigned_user_id];

        if (Schema::hasColumn('leads', 'assigned_by')) {
            $payload['assigned_by'] = auth()->id();
        }
        if (Schema::hasColumn('leads', 'assigned_at')) {
            $payload['assigned_at'] = now();
        }

        $lead->update($payload);
        return back()->with('success', 'Lead assigned to user successfully.');
    }

    /* ===================== Comments / Calls ===================== */

    public function commentStore(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'comment'      => 'required|string|max:2000',
            'next_call_at' => 'nullable|date',
        ]);

        LeadComment::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'comment' => $data['comment'],
        ]);

        if (!$this->isLocked($lead) && !empty($data['next_call_at'])) {
            $lead->next_call_at = $data['next_call_at'];
            if (in_array($lead->status, ['new', 'follow_up'], true)) {
                $lead->status = 'follow_up';
            }
            $lead->save();
        }

        return back()->with('success', 'Comment added successfully.');
    }

    public function commentDestroy(LeadComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    public function callLogStore(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'called_at'    => 'required|date',
            'duration_sec' => 'nullable|integer|min:0',
            'outcome'      => 'nullable|in:answered,no_answer,busy,wrong_number,voicemail',
            'notes'        => 'nullable|string',
            'next_call_at' => 'nullable|date',
        ]);

        LeadCallLog::create([
            'lead_id'      => $lead->id,
            'user_id'      => auth()->id(),
            'called_at'    => $data['called_at'],
            'duration_sec' => $data['duration_sec'] ?? 0,
            'outcome'      => $data['outcome'] ?? 'answered',
            'notes'        => $data['notes'] ?? null,
        ]);

        if (!$this->isLocked($lead)) {
            $lead->status = 'follow_up';
            if (!empty($data['next_call_at'])) {
                $lead->next_call_at = $data['next_call_at'];
            }
            $lead->save();
        }

        return back()->with('success', 'Call logged successfully.');
    }

    /* ===================== Status / Accept ===================== */

    public function setStatus(Request $request, Lead $lead)
    {
        $request->validate(['status' => 'required|in:new,follow_up,accept,reject,lost']);

        if ($this->isLocked($lead) && $request->status !== $lead->status) {
            return back()->with('error', 'Status is locked (accept/reject).');
        }

        if ($request->status === 'accept') {
            $qr = null;

            $client = null;
            if (!empty($lead->email)) {
                $client = Client::where('email', $lead->email)->first();
            }

            if ($client) {
                $qr = $this->createQrAndEmailAlways($client, $lead);
            } else {
                $qr = $this->createQrForLeadOnly($lead);
            }

            $this->updateLeadQrId($lead, $qr['id'] ?? null);
            $lead->update(['status' => 'accept']);

            $watiResult = $this->trySendWatiForLead($lead, $qr);

            return back()->with([
                'success'     => 'Status updated to accept successfully.',
                'qr_popup'    => $qr + ['lead_name' => $lead->name],
                'wati_result' => $watiResult,
            ]);
        }

        $lead->update(['status' => $request->status]);
        return back()->with('success', 'Status updated successfully.');
    }

    public function accept(Request $request, Lead $lead)
    {
        if ($this->isLocked($lead) && $lead->status !== 'accept') {
            return back()->with('error', 'Cannot accept: status is locked.');
        }

        DB::beginTransaction();
        try {
            $payload = [
                'name'         => (string) $lead->name,
                'email'        => (string) $lead->email,
                'phone'        => (string) $lead->phone,
                'job'          => (string) $lead->job,
                'active'       => true,
                'country_code' => Schema::hasColumn('leads', 'country_code')
                    ? (string) ($lead->country_code ?? '')
                    : null,
            ];

            $rules = [
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:clients,email',
                'phone' => 'nullable|string|max:20',
                'job'   => 'nullable|string|max:255',
            ];

            $validator = Validator::make($payload, $rules);
            if ($validator->fails()) {
                if (!empty($payload['email']) && Client::where('email', $payload['email'])->exists()) {
                    DB::rollBack();
                    return back()->with('error', 'Email already exists for another client.');
                }
                DB::rollBack();
                return back()->with('error', 'There is an issue with name, email, or phone.');
            }

            $clientData = [
                'name'              => $payload['name'],
                'email'             => $payload['email'],
                'phone'             => $payload['phone'],
                'job'               => $payload['job'],
                'status'            => 'verified',
                'email_verified_at' => now(),
            ];

            if (Schema::hasColumn('clients', 'active')) {
                $clientData['active'] = (bool) $payload['active'];
            }
            if (Schema::hasColumn('clients', 'country_code')) {
                $clientData['country_code'] = (string) $payload['country_code'];
            }
            if (Schema::hasColumn('clients', 'type')) {
                $clientData['type'] = 1;
            }

            $client = Client::create($clientData);

            if ($lead->status !== 'accept') {
                $lead->update(['status' => 'accept']);
            }

            $qr = $this->createQrAndEmailAlways($client, $lead);
            $this->updateLeadQrId($lead, $qr['id'] ?? null);
            $this->upsertAdminSnapshot($client);

            DB::commit();

            $watiResult = $this->trySendWatiForLead($lead, $qr);

            return back()->with([
                'success'     => 'Lead accepted, client created, and QR email sent successfully.',
                'qr_popup'    => $qr + ['lead_name' => $lead->name],
                'wati_result' => $watiResult,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Acceptance failed: ' . $e->getMessage());
        }
    }

    /* ===================== Export / Import ===================== */

    public function export(Request $request)
    {
        $filters = $this->extractFilters($request);
        return Excel::download(new LeadsExport($filters), 'leads_export_' . date('Ymd_His') . '.xlsx');
    }

    public function exportaccepted(Request $request)
    {
        $filters = $this->extractFilters($request);
        // الحالة الصحيحة في النظام هي 'accept'
        $filters['status'] = 'accept';

        return Excel::download(new LeadsExport($filters), 'leads_export_' . date('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);
        $userId = auth()->id();

        try {
            Excel::import(new \App\Imports\LeadsImport, $request->file('file'));

            Lead::query()
                ->whereNull('assigned_user_id')
                ->where('created_at', '>=', now()->subMinutes(15))
                ->update(['assigned_user_id' => $userId]);

            if (Schema::hasColumn('leads', 'created_by')) {
                Lead::query()
                    ->whereNull('created_by')
                    ->where('created_at', '>=', now()->subMinutes(15))
                    ->update(['created_by' => $userId]);
            }

            if (Schema::hasColumn('leads', 'assigned_by')) {
                Lead::query()
                    ->whereNull('assigned_by')
                    ->where('created_at', '>=', now()->subMinutes(15))
                    ->update(['assigned_by' => $userId]);
            }
            if (Schema::hasColumn('leads', 'assigned_at')) {
                Lead::query()
                    ->whereNull('assigned_at')
                    ->where('created_at', '>=', now()->subMinutes(15))
                    ->update(['assigned_at' => now()]);
            }

            return back()->with('success', 'Leads imported successfully.');
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /* ===================== QR Helpers & Email ===================== */

    protected function createQrAndEmailAlways(Client $client, Lead $lead): array
    {
        $cc   = Schema::hasColumn('clients', 'country_code') ? (string) ($client->country_code ?? '') : '';
        $raw  = $cc . (string) ($client->phone ?? '');
        $only = preg_replace('/\D+/', '', $raw ?? '');

        $shortCode = $only ? substr($only, -8) : strtoupper(Str::random(8));

        if (class_exists(QrcodeModel::class)) {
            $base = $shortCode;
            $i = 0;
            while (QrcodeModel::where('short_code', $shortCode)->exists() && $i < 5) {
                $shortCode = $base . rand(0, 9);
                $i++;
            }
        }

        $qrUrl = url('/qr/' . $shortCode);

        $directory = public_path('qrcodes');
        if (!file_exists($directory)) @mkdir($directory, 0755, true);

        $fileName   = 'qr_' . time() . '_' . $client->id . '.png';
        $filePath   = $directory . '/' . $fileName;
        $qrImageUrl = asset('public/qrcodes/' . $fileName);

        try {
            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
            } elseif (class_exists(\QrCode::class)) {
                \QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
            } else {
                Log::warning('QR package not found, skipped QR generation', ['client_id' => $client->id]);
                $qrImageUrl = null;
            }
        } catch (Throwable $e) {
            Log::error('QR generation failed', ['client_id' => $client->id, 'error' => $e->getMessage()]);
        }

        $qrcodeId = null;
        if (class_exists(QrcodeModel::class)) {
            $payload = [
                'qrcode'      => $fileName,
                'active'      => 1,
                'short_code'  => $shortCode,
                'email'       => $client->email,
                'scan'        => 0,
                'register_id' => $client->id,
            ];
            if (Schema::hasColumn((new QrcodeModel)->getTable(), 'type')) {
                $payload['type'] = 1;
            }

            QrcodeModel::where('register_id', $client->id)->delete();
            $qrRow    = QrcodeModel::create($payload);
            $qrcodeId = $qrRow->id ?? null;
        }

        $emailTo = (string) ($lead->email ?? $client->email ?? '');
        if ($emailTo !== '' && class_exists(\App\Mail\ClientQrMail::class)) {
            try {
                Mail::to($emailTo)->send(new \App\Mail\ClientQrMail($client, $qrUrl, $qrImageUrl));
            } catch (Throwable $e) {
                Log::error('ClientQrMail FAILED', [
                    'client_id' => $client->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return [
            'id'           => $qrcodeId,
            'qr_url'       => $qrUrl,
            'qr_image_url' => $qrImageUrl,
            'short_code'   => $shortCode,
            'file_name'    => $fileName,
        ];
    }

    protected function createQrForLeadOnly(Lead $lead): array
    {
        $only      = preg_replace('/\D+/', '', (string)($lead->phone ?? ''));
        $shortCode = $only ? substr($only, -8) : strtoupper(Str::random(8));

        if (class_exists(QrcodeModel::class)) {
            $base = $shortCode;
            $i = 0;
            while (QrcodeModel::where('short_code', $shortCode)->exists() && $i < 5) {
                $shortCode = $base . rand(0, 9);
                $i++;
            }
        }

        $qrUrl = url('/qr/' . $shortCode);

        $directory = public_path('qrcodes');
        if (!file_exists($directory)) @mkdir($directory, 0755, true);

        $fileName   = 'qr_' . time() . '_lead_' . $lead->id . '.png';
        $filePath   = $directory . '/' . $fileName;
        $qrImageUrl = asset('public/qrcodes/' . $fileName);

        try {
            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
            } elseif (class_exists(\QrCode::class)) {
                \QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
            } else {
                Log::warning('QR package not found, skipped QR generation (lead)', ['lead_id' => $lead->id]);
                $qrImageUrl = null;
            }
        } catch (Throwable $e) {
            Log::error('QR generation failed (lead)', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }

        $qrcodeId = null;
        if (class_exists(QrcodeModel::class)) {
            $payload = [
                'qrcode'      => $fileName,
                'active'      => 1,
                'short_code'  => $shortCode,
                'email'       => $lead->email,
                'scan'        => 0,
                'register_id' => $lead->id,
            ];
            if (Schema::hasColumn((new QrcodeModel)->getTable(), 'type')) {
                $payload['type'] = 1;
            }

            if (!empty($lead->email)) {
                QrcodeModel::where('email', $lead->email)->delete();
            }

            $qrRow    = QrcodeModel::create($payload);
            $qrcodeId = $qrRow->id ?? null;
        }

        return [
            'id'           => $qrcodeId,
            'qr_url'       => $qrUrl,
            'qr_image_url' => $qrImageUrl,
            'short_code'   => $shortCode,
            'file_name'    => $fileName,
        ];
    }

    protected function upsertAdminSnapshot(Client $client): void
    {
        $tables = [];
        if (Schema::hasTable('admins')) $tables[] = 'admins';
        if (Schema::hasTable('admin'))  $tables[] = 'admin';

        foreach ($tables as $table) {
            $data = [
                'name'       => $client->name,
                'email'      => $client->email,
                'phone'      => $client->phone,
                'updated_at' => now(),
            ];
            if (Schema::hasColumn($table, 'type')) {
                $data['type'] = 1;
            }
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = now();
            }

            try {
                $exists = DB::table($table)->where('email', $client->email)->exists();
                if ($exists) {
                    DB::table($table)->where('email', $client->email)->update($data);
                } else {
                    DB::table($table)->insert($data);
                }
            } catch (Throwable $e) {
                Log::error("Failed to upsert into {$table}", ['error' => $e->getMessage()]);
            }
        }
    }

    /* ===================== WATI Helper ===================== */

    protected function trySendWatiForLead(Lead $lead, array $qr): array
    {
        $svc = new WatiService();

        $to = WatiService::sanitizeMsisdn($lead->phone ?? '');
        if (empty($qr['qr_image_url'])) {
            Log::warning('WATI: skip because qr_image_url missing', ['lead_id' => $lead->id]);
            return ['ok' => false, 'skipped' => true, 'reason' => 'qr_image_missing'];
        }
        if (!$svc->isReady()) {
            Log::warning('WATI: skip because service not ready');
            return ['ok' => false, 'skipped' => true, 'reason' => 'service_not_ready'];
        }
        if ($to === '') {
            Log::warning('WATI: skip because recipient phone empty', ['lead_id' => $lead->id]);
            return ['ok' => false, 'skipped' => true, 'reason' => 'no_phone'];
        }

        $template  = (string) config('services.wati.template_name');
        $broadcast = (string) (config('services.wati.broadcast_prefix', 'broadcast') . '_' . now()->format('Ymd_His'));

        $parameters = [
            ['name' => '1', 'value' => (string) $lead->name],
            ['name' => '2', 'value' => (string) config('services.wati.event_name')],
            ['name' => '3', 'value' => (string) config('services.wati.event_date')],
            ['name' => '4', 'value' => (string) config('services.wati.event_time')],
            ['name' => '5', 'value' => (string) config('services.wati.event_location')],
        ];

        return $svc->sendTemplate(
            toWhatsapp: $to,
            templateName: $template,
            broadcastName: $broadcast,
            parameters: $parameters,
            headerImageUrl: $qr['qr_image_url'] ?? null
        );
    }

    /* ===================== Accepted Leads ===================== */

    public function accepted(Request $request)
    {
        $this->autoMarkLost();

        $q = Lead::query()
            ->with(['assignedUser:id,name'])
            ->where('status', 'accept')
            ->when($request->filled('q'), function ($x) use ($request) {
                $s = trim((string) $request->q);
                $x->where(function ($y) use ($s) {
                    $y->where('name',  'like', "%{$s}%")
                      ->orWhere('email','like', "%{$s}%")
                      ->orWhere('phone','like', "%{$s}%")
                      ->orWhere('job',  'like', "%{$s}%");
                });
            });

        $this->applyAssignedUserFilter($q, $request);

        $q->when(Schema::hasColumn('leads', 'assigned_by') && ($request->filled('assigned_by') || $request->filled('assigned_by_id')), function ($x) use ($request) {
                $val = $request->input('assigned_by_id', $request->input('assigned_by'));
                $x->where('assigned_by', (int) $val);
            })
          ->when($request->filled('from'), fn($x) => $x->whereDate('created_at', '>=', $request->from))
          ->when($request->filled('to'),   fn($x) => $x->whereDate('created_at', '<=', $request->to))
          ->latest();

        // نفس فكرة التوريث
        $leads = $q->paginate(15)->withQueryString();

        return view('content.customer.accepted', [
            'pageTitle' => 'Accepted Leads',
            'leads'     => $leads,
            'filters'   => $this->extractFilters($request),
        ]);
    }
}
