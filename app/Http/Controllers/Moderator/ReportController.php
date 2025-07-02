<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Report::with(['reportable', 'user', 'moderator']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== '') {
            $reportableType = $request->type === 'thread' ? Thread::class : Comment::class;
            $query->where('reportable_type', $reportableType);
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Get reports
        $reports = $query->paginate(15);

        return view('moderator.reports.index', compact('reports'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Load related data
        $report->load(['reportable', 'user', 'moderator']);

        return view('moderator.reports.show', compact('report'));
    }

    /**
     * Approve a report and take action against reported content.
     */
    public function approve(Request $request, Report $report)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:delete,edit,flag'],
            'reason' => ['required', 'string'],
            'notify_reporter' => ['nullable', 'boolean'],
            'notify_content_owner' => ['nullable', 'boolean'],
        ]);

        $reportable = $report->reportable;

        if (!$reportable) {
            return redirect()->route('moderator.reports.index')
                             ->with('error', 'Konten yang dilaporkan tidak ditemukan!');
        }

        // Take action based on selected option
        switch ($validated['action']) {
            case 'delete':
                if (method_exists($reportable, 'delete')) {
                    // Save moderation data before deleting
                    if (method_exists($reportable, 'update')) {
                        $reportable->update([
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                            'moderation_reason' => $validated['reason'],
                        ]);
                    }

                    $reportable->delete();
                }
                break;

            case 'edit':
                if (method_exists($reportable, 'update')) {
                    $reportable->update([
                        'is_flagged' => true,
                        'flagged_by' => auth()->id(),
                        'flagged_at' => now(),
                        'flag_reason' => $validated['reason'],
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                    ]);
                }
                break;

            case 'flag':
                if (method_exists($reportable, 'update')) {
                    $reportable->update([
                        'is_flagged' => true,
                        'flagged_by' => auth()->id(),
                        'flagged_at' => now(),
                        'flag_reason' => $validated['reason'],
                    ]);
                }
                break;
        }

        // Update report status
        $report->update([
            'status' => 'approved',
            'resolution' => $validated['action'],
            'resolution_note' => $validated['reason'],
            'moderator_id' => auth()->id(),
            'resolved_at' => now(),
        ]);

        // Notify reporter if requested
        if ($request->has('notify_reporter') && $request->notify_reporter && $report->user) {
            $report->user->notify(new ReportResolvedNotification(
                $report,
                'approved',
                $validated['action'],
                $validated['reason']
            ));
        }

        // Notify content owner if requested
        if ($request->has('notify_content_owner') && $request->notify_content_owner && $reportable->user) {
            $contentType = $report->reportable_type === Thread::class ? 'thread' : 'comment';
            $reportable->user->notify(new ReportResolvedNotification(
                $report,
                'content_moderated',
                $validated['action'],
                $validated['reason'],
                $contentType
            ));
        }

        return redirect()->route('moderator.reports.index')
                         ->with('success', 'Laporan berhasil disetujui dan ditindaklanjuti!');
    }

    /**
     * Reject a report (no action needed).
     */
    public function reject(Request $request, Report $report)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
            'notify_reporter' => ['nullable', 'boolean'],
        ]);

        // Update report status
        $report->update([
            'status' => 'rejected',
            'resolution' => 'no_action',
            'resolution_note' => $validated['reason'],
            'moderator_id' => auth()->id(),
            'resolved_at' => now(),
        ]);

        // Notify reporter if requested
        if ($request->has('notify_reporter') && $request->notify_reporter && $report->user) {
            $report->user->notify(new ReportResolvedNotification(
                $report,
                'rejected',
                'no_action',
                $validated['reason']
            ));
        }

        return redirect()->route('moderator.reports.index')
                         ->with('success', 'Laporan berhasil ditolak!');
    }

    /**
     * Batch update reports.
     */
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'report_ids' => ['required', 'array'],
            'report_ids.*' => ['required', 'exists:reports,id'],
            'action' => ['required', 'in:approve,reject'],
            'resolution' => ['required_if:action,approve', 'in:delete,edit,flag,no_action'],
            'reason' => ['required', 'string'],
            'notify_users' => ['nullable', 'boolean'],
        ]);

        $count = count($validated['report_ids']);
        $action = $validated['action'];
        $resolution = $validated['resolution'] ?? 'no_action';
        $reason = $validated['reason'];
        $notifyUsers = $validated['notify_users'] ?? false;

        foreach ($validated['report_ids'] as $reportId) {
            $report = Report::find($reportId);

            if (!$report) continue;

            if ($action === 'approve' && $resolution !== 'no_action') {
                $reportable = $report->reportable;

                if (!$reportable) continue;

                // Take action based on selected resolution
                switch ($resolution) {
                    case 'delete':
                        if (method_exists($reportable, 'delete')) {
                            $reportable->delete();
                        }
                        break;

                    case 'edit':
                    case 'flag':
                        if (method_exists($reportable, 'update')) {
                            $reportable->update([
                                'is_flagged' => true,
                                'flagged_by' => auth()->id(),
                                'flagged_at' => now(),
                                'flag_reason' => $reason,
                            ]);
                        }
                        break;
                }

                $report->update([
                    'status' => 'approved',
                    'resolution' => $resolution,
                    'resolution_note' => $reason,
                    'moderator_id' => auth()->id(),
                    'resolved_at' => now(),
                ]);

            } else {
                // Reject report
                $report->update([
                    'status' => 'rejected',
                    'resolution' => 'no_action',
                    'resolution_note' => $reason,
                    'moderator_id' => auth()->id(),
                    'resolved_at' => now(),
                ]);
            }

            // Send notifications if requested
            if ($notifyUsers) {
                if ($report->user) {
                    $report->user->notify(new ReportResolvedNotification(
                        $report,
                        $action === 'approve' ? 'approved' : 'rejected',
                        $resolution,
                        $reason
                    ));
                }

                if ($action === 'approve' && $resolution !== 'no_action' && $reportable && $reportable->user) {
                    $contentType = $report->reportable_type === Thread::class ? 'thread' : 'comment';
                    $reportable->user->notify(new ReportResolvedNotification(
                        $report,
                        'content_moderated',
                        $resolution,
                        $reason,
                        $contentType
                    ));
                }
            }
        }

        $actionText = $action === 'approve' ? 'ditindaklanjuti' : 'ditolak';

        return redirect()->route('moderator.reports.index')
                         ->with('success', "{$count} laporan berhasil {$actionText}!");
    }
}
