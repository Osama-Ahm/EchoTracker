<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Redirect admin users to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Get statistics
        $totalIncidents = Incident::count();
        $myIncidents = Incident::where('user_id', $user->id)->count();
        $recentIncidents = Incident::with(['category', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Status statistics
        $statusStats = Incident::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Category statistics
        $categoryStats = Incident::with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->count];
            });

        // Get gamification stats
        $gamificationStats = [
            'total_points' => $user->total_points,
            'rank' => $user->rank,
            'badges_count' => $user->badges()->count(),
            'recent_badges' => $user->recent_badges,
        ];

        // Get recent community activity
        $recentForumTopics = \App\Models\ForumTopic::with(['forum', 'user'])
            ->orderBy('last_activity_at', 'desc')
            ->limit(3)
            ->get();

        $upcomingEvents = \App\Models\CommunityEvent::with('organizer')
            ->where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'totalIncidents',
            'myIncidents',
            'recentIncidents',
            'statusStats',
            'categoryStats',
            'gamificationStats',
            'recentForumTopics',
            'upcomingEvents'
        ));
    }
}
