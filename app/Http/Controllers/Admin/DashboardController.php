<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Count statistics for dashboard
        $stats = [
            'users' => User::count(),
            'threads' => Thread::count(),
            'comments' => Comment::count(),
            'categories' => Category::count()
        ];

        // Get recent users
        $latestUsers = User::latest()->take(5)->get();

        // Get recent threads
        $latestThreads = Thread::with(['user', 'category'])
                               ->latest()
                               ->take(5)
                               ->get();

        // Get recent comments
        $latestComments = Comment::with(['user', 'thread'])
                                 ->latest()
                                 ->take(5)
                                 ->get();

        // Get monthly activity data for chart
        $monthlyData = $this->getMonthlyActivityData();

        return view('admin.dashboard', compact(
            'stats',
            'latestUsers',
            'latestThreads',
            'latestComments',
            'monthlyData'
        ));
    }

    private function getMonthlyActivityData()
    {
        $data = [];

        // Get current year
        $year = now()->year;

        // Get monthly user registrations
        $userRegistrations = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                                ->whereYear('created_at', $year)
                                ->groupBy('month')
                                ->pluck('count', 'month')
                                ->toArray();

        // Get monthly thread creations
        $threadCreations = Thread::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                                ->whereYear('created_at', $year)
                                ->groupBy('month')
                                ->pluck('count', 'month')
                                ->toArray();

        // Get monthly comment creations
        $commentCreations = Comment::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                                  ->whereYear('created_at', $year)
                                  ->groupBy('month')
                                  ->pluck('count', 'month')
                                  ->toArray();

        // Fill in missing months with zeros
        for ($month = 1; $month <= 12; $month++) {
            $data['users'][$month] = $userRegistrations[$month] ?? 0;
            $data['threads'][$month] = $threadCreations[$month] ?? 0;
            $data['comments'][$month] = $commentCreations[$month] ?? 0;
        }

        return $data;
    }
}
