@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Community Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="text-eco-primary mb-2 animate-fade-in">
                    <i class="bi bi-people me-2"></i>
                    Community Hub
                </h1>
                <p class="text-muted mb-0 animate-fade-in">Connect, collaborate, and make a difference together</p>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-trophy-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-warning">{{ $userStats['total_points'] }}</h2>
                    <p class="card-text text-muted mb-0">Your Points</p>
                    <small class="text-muted">Rank #{{ $userStats['rank'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-award-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-primary">{{ $userStats['badges_count'] }}</h2>
                    <p class="card-text text-muted mb-0">Badges Earned</p>
                    <small class="text-muted">Keep going!</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-success">{{ $communityStats['total_members'] }}</h2>
                    <p class="card-text text-muted mb-0">Community Members</p>
                    <small class="text-muted">Growing strong</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-chat-dots-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-info">{{ $communityStats['total_topics'] }}</h2>
                    <p class="card-text text-muted mb-0">Forum Topics</p>
                    <small class="text-muted">Join the discussion</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('forums.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-chat-dots mb-2" style="font-size: 2rem;"></i>
                                <span>Browse Forums</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('community.events.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-calendar-event mb-2" style="font-size: 2rem;"></i>
                                <span>Find Events</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('community.volunteer.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-heart mb-2" style="font-size: 2rem;"></i>
                                <span>Volunteer</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('community.leaderboard') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-trophy mb-2" style="font-size: 2rem;"></i>
                                <span>Leaderboard</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Badges -->
    @if(count($userStats['recent_badges']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-award me-2"></i>Recent Badges
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($userStats['recent_badges'] as $badge)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="me-3" style="color: {{ $badge->color }};">
                                    <i class="{{ $badge->icon }}" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $badge->name }}</h6>
                                    <small class="text-muted">{{ $badge->description }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('community.badges') }}" class="btn btn-eco-primary">
                            <i class="bi bi-award me-2"></i>View All Badges
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="row">
        <!-- Recent Forum Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-chat-dots me-2"></i>Recent Forum Activity
                    </h5>
                    <a href="{{ route('forums.index') }}" class="btn btn-sm btn-outline-eco-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentTopics->count() > 0)
                        @foreach($recentTopics as $topic)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-chat"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('forums.topics.show', $topic) }}" class="text-decoration-none">
                                        {{ Str::limit($topic->title, 50) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    by {{ $topic->user->name }} in {{ $topic->forum->name }}
                                    <br>{{ $topic->last_activity_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No recent forum activity</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-calendar-event me-2"></i>Upcoming Events
                    </h5>
                    <a href="{{ route('community.events.index') }}" class="btn btn-sm btn-outline-eco-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        @foreach($upcomingEvents as $event)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-calendar"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('community.events.show', $event) }}" class="text-decoration-none">
                                        {{ Str::limit($event->title, 50) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $event->start_date->format('M j, Y g:i A') }}
                                    <br>{{ $event->location }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No upcoming events</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard and Volunteer Opportunities -->
    <div class="row">
        <!-- Community Leaderboard -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-trophy me-2"></i>Community Leaders
                    </h5>
                    <a href="{{ route('community.leaderboard') }}" class="btn btn-sm btn-outline-eco-primary">View All</a>
                </div>
                <div class="card-body">
                    @foreach($leaderboard->take(5) as $index => $leader)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }} rounded-pill">
                                #{{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $leader->name }}</h6>
                            <small class="text-muted">{{ number_format($leader->total_points) }} points</small>
                        </div>
                        @if($index < 3)
                            <div class="text-warning">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Volunteer Opportunities -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-heart me-2"></i>Volunteer Opportunities
                    </h5>
                    <a href="{{ route('community.volunteer.index') }}" class="btn btn-sm btn-outline-eco-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($volunteerOpportunities->count() > 0)
                        @foreach($volunteerOpportunities as $opportunity)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-heart"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('community.volunteer.show', $opportunity) }}" class="text-decoration-none">
                                        {{ Str::limit($opportunity->title, 50) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $opportunity->start_date->format('M j, Y') }}
                                    <br>{{ $opportunity->location }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No volunteer opportunities available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-eco-primary {
    color: var(--eco-primary);
    border-color: var(--eco-primary);
}

.btn-outline-eco-primary:hover {
    background-color: var(--eco-primary);
    border-color: var(--eco-primary);
    color: white;
}
</style>
@endsection
