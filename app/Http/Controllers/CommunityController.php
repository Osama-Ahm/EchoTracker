<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Badge;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\CommunityEvent;
use App\Models\VolunteerOpportunity;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // User's gamification stats
        $userStats = [
            'total_points' => 0, // Will be implemented when user points system is ready
            'rank' => 0,
            'recent_badges' => [],
            'badges_count' => 0,
        ];

        // Recent community activity
        $recentTopics = ForumTopic::with(['forum', 'user'])
            ->orderBy('last_activity_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingEvents = CommunityEvent::with('organizer')
            ->where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $volunteerOpportunities = VolunteerOpportunity::with('creator')
            ->where('status', 'active')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Community leaderboard (using incident count for now)
        $leaderboard = User::withCount('incidents')
            ->where('role', '!=', 'admin')
            ->orderBy('incidents_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->total_points = $user->incidents_count * 10; // 10 points per incident
                return $user;
            });

        // Community stats
        $communityStats = [
            'total_members' => User::where('role', '!=', 'admin')->count(),
            'active_forums' => Forum::where('is_active', true)->count(),
            'total_topics' => ForumTopic::count(),
            'upcoming_events' => CommunityEvent::where('status', 'upcoming')->count(),
            'active_volunteers' => VolunteerOpportunity::where('status', 'active')->count(),
        ];

        return view('community.index', compact(
            'userStats',
            'recentTopics',
            'upcomingEvents',
            'volunteerOpportunities',
            'leaderboard',
            'communityStats'
        ));
    }

    public function leaderboard()
    {
        // For now, show users with their incident count as points
        $leaderboard = User::withCount('incidents')
            ->where('role', '!=', 'admin')
            ->orderBy('incidents_count', 'desc')
            ->paginate(50);

        // Add total_points property for the view
        $leaderboard->getCollection()->transform(function ($user) {
            $user->total_points = $user->incidents_count * 10; // 10 points per incident
            return $user;
        });

        return view('community.leaderboard', compact('leaderboard'));
    }

    public function badges()
    {
        $badges = Badge::where('is_active', true)->orderBy('points_required')->get();

        return view('community.badges', compact('badges'));
    }

}
