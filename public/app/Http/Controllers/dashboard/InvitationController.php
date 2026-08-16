<?php

// app/Http/Controllers/Dashboard/InvitationController.php
namespace App\Http\Controllers\dashboard;

use App\Exports\InvitationsExport;
use App\Exports\InvitationsTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationRequest;
use App\Imports\InvitationsImport;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InvitationController extends Controller
{
    // List + filters
    public function index(Request $request)
    {
        $search     = trim((string)$request->get('q', ''));
        $type       = $request->get('type');
        $attendance = $request->get('attendance', 'all'); // all|present|absent

        $query = Invitation::query();

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('invitation_number', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($attendance === 'present') $query->where('attendance', true);
        if ($attendance === 'absent')  $query->where('attendance', false);

        $invitations = $query->latest('id')->paginate(20)->withQueryString();

        // quick stats
        $stats = [
            'total'   => Invitation::count(),
            'present' => Invitation::where('attendance', true)->count(),
            'absent'  => Invitation::where('attendance', false)->count(),
        ];

        return view('content.invitations.index', compact('invitations','stats','search','type','attendance'));
    }

    public function create()
    {
        return view('content.invitations.create');
    }

    public function store(InvitationRequest $request)
    {
        Invitation::create($request->validated());
        return back()->with('success', __('Saved'));
    }

    public function edit(Invitation $invitation)
    {
        return view('content.invitations.edit', compact('invitation'));
    }

    public function update(InvitationRequest $request, Invitation $invitation)
    {
        $invitation->update($request->validated());
        return back()->with('success', __('Updated'));
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();
        return back()->with('success', __('Deleted'));
    }

    // Mark attendance (toggle or set)
    public function markAttendance(Request $request, Invitation $invitation)
    {
        $value = $request->has('attendance')
            ? (bool)$request->boolean('attendance')
            : ! $invitation->attendance; // toggle if not provided

        $invitation->update(['attendance' => $value]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'attendance' => $invitation->attendance]);
        }
        return back()->with('success', __('Attendance updated'));
    }

    // Import from Excel
    public function import(Request $request)
    {
        $request->validate(['file' => ['required','file','mimes:xlsx,xls,csv']]);
        DB::transaction(function () use ($request) {
            Excel::import(new InvitationsImport, $request->file('file'));
        });
        return back()->with('success', __('Imported successfully'));
    }

    // Export to Excel (respects filters/search)
    public function export(Request $request)
    {
        $search     = $request->get('q');
        $type       = $request->get('type');
        $attendance = $request->get('attendance', 'all');

        return Excel::download(
            new InvitationsExport($search, $type, $attendance),
            'invitations.xlsx'
        );
    }

    // Download template (headings)
    public function template()
    {
        return Excel::download(new InvitationsTemplateExport(), 'invitations_template.xlsx');
    }
}
