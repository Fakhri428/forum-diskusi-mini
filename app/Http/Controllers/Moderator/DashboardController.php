<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the moderator dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get basic statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'active_threads' => \App\Models\Thread::where('is_approved', true)->count(),
            'pending_moderations' => \App\Models\Thread::where('is_approved', false)->count(),
        ];

        // Count threads, comments, and reports
        $threadCount = \App\Models\Thread::count();
        $commentCount = \App\Models\Comment::count();
        $reportCount = \App\Models\Report::where('status', 'pending')->count();

        // Get pending threads
        $pendingThreads = Thread::with(['user', 'category'])
                              ->where('is_approved', false)
                              ->latest()
                              ->take(5)
                              ->get();

        // Get latest comments
        $latestComments = Comment::with(['user', 'thread'])
                              ->latest()
                              ->take(5)
                              ->get();

        // Rename to recentComments for consistency with view
        $recentComments = $latestComments;

        // Get recent reports
        $recentReports = Report::with(['reportable', 'user', 'moderator'])
                              ->where('status', 'pending')
                              ->latest()
                              ->take(5)
                              ->get();

        // Get moderation activity data for chart
        $moderationActivity = $this->getModerationActivityData();

        return view('moderator.dashboard', compact(
            'stats',
            'threadCount',
            'commentCount',
            'reportCount',
            'pendingThreads',
            'recentComments',
            'recentReports',
            'moderationActivity'
        ));
    }

    private function getModerationActivityData()
    {
        $data = [];
        $days = 7; // Last 7 days

        // Get dates for the last N days
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('Y-m-d');
        }

        // Thread approvals per day
        $threadApprovals = Thread::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                              ->where('is_approved', true)
                              ->whereDate('updated_at', '>=', now()->subDays($days - 1)->format('Y-m-d'))
                              ->groupBy('date')
                              ->pluck('count', 'date')
                              ->toArray();

        // Comment deletions per day
        $commentDeletions = Comment::selectRaw('DATE(deleted_at) as date, COUNT(*) as count')
                                ->whereNotNull('deleted_at')
                                ->whereDate('deleted_at', '>=', now()->subDays($days - 1)->format('Y-m-d'))
                                ->groupBy('date')
                                ->pluck('count', 'date')
                                ->toArray();

        // Reports resolved per day
        $reportsResolved = Report::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                              ->whereIn('status', ['approved', 'rejected'])
                              ->whereDate('updated_at', '>=', now()->subDays($days - 1)->format('Y-m-d'))
                              ->groupBy('date')
                              ->pluck('count', 'date')
                              ->toArray();

        // Format data for chart
        foreach ($dates as $date) {
            $data['dates'][] = date('d M', strtotime($date));
            $data['thread_approvals'][] = $threadApprovals[$date] ?? 0;
            $data['comment_deletions'][] = $commentDeletions[$date] ?? 0;
            $data['reports_resolved'][] = $reportsResolved[$date] ?? 0;
        }

        // Deleted comments data
        $deletedComments = DB::table('comments')
            ->selectRaw('DATE(deleted_at) as date, COUNT(*) as count')
            ->whereNotNull('deleted_at')
            ->whereRaw('DATE(deleted_at) >= ?', [now()->subDays(7)->toDateString()])
            ->groupBy('date')
            ->get();

        return $data;
    }
}
