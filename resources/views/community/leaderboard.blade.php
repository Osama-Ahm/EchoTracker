@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-trophy me-2"></i>Community Leaderboard
            </h1>
            <p class="text-muted">Top contributors to our environmental community</p>
        </div>
        <a href="{{ route('community.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Community
        </a>
    </div>

    <!-- Top 3 Users -->
    @if($leaderboard->count() >= 3)
        <div class="row mb-5">
            <!-- 2nd Place -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <div class="bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                            </div>
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-secondary rounded-pill">2nd</span>
                            </div>
                        </div>
                        <h5 class="card-title">{{ $leaderboard->skip(1)->first()->name }}</h5>
                        <p class="text-muted mb-2">{{ number_format($leaderboard->skip(1)->first()->total_points) }} points</p>
                        <i class="bi bi-award text-secondary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>

            <!-- 1st Place -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-lg text-center h-100 border-warning">
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <div class="bg-warning rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-warning rounded-pill">1st</span>
                            </div>
                        </div>
                        <h4 class="card-title text-warning">{{ $leaderboard->first()->name }}</h4>
                        <p class="text-muted mb-2">{{ number_format($leaderboard->first()->total_points) }} points</p>
                        <i class="bi bi-trophy text-warning" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>

            <!-- 3rd Place -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <div class="bg-warning rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; opacity: 0.7;">
                                <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                            </div>
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-warning rounded-pill" style="opacity: 0.7;">3rd</span>
                            </div>
                        </div>
                        <h5 class="card-title">{{ $leaderboard->skip(2)->first()->name }}</h5>
                        <p class="text-muted mb-2">{{ number_format($leaderboard->skip(2)->first()->total_points) }} points</p>
                        <i class="bi bi-award" style="font-size: 2rem; color: #cd7f32;"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Full Leaderboard -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-list-ol me-2"></i>Full Rankings
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Rank</th>
                            <th class="border-0">User</th>
                            <th class="border-0">Points</th>
                            <th class="border-0">Badge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $index => $user)
                            <tr class="{{ Auth::id() == $user->id ? 'table-warning' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($index < 3)
                                            @if($index == 0)
                                                <i class="bi bi-trophy text-warning me-2"></i>
                                            @elseif($index == 1)
                                                <i class="bi bi-award text-secondary me-2"></i>
                                            @else
                                                <i class="bi bi-award me-2" style="color: #cd7f32;"></i>
                                            @endif
                                        @endif
                                        <span class="fw-bold">#{{ $index + 1 }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            @if(Auth::id() == $user->id)
                                                <small class="text-primary">(You)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-eco-primary fs-6">{{ number_format($user->total_points) }}</span>
                                </td>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning">Champion</span>
                                    @elseif($index < 3)
                                        <span class="badge bg-secondary">Top Contributor</span>
                                    @elseif($index < 10)
                                        <span class="badge bg-success">Active Member</span>
                                    @else
                                        <span class="badge bg-info">Community Member</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($leaderboard->hasPages())
            <div class="card-footer bg-white">
                {{ $leaderboard->links() }}
            </div>
        @endif
    </div>

    <!-- Points Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>How to Earn Points
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Report an incident: <strong>10 points</strong></li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Start a forum topic: <strong>10 points</strong></li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Reply to a topic: <strong>5 points</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Organize an event: <strong>25 points</strong></li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Attend an event: <strong>15 points</strong></li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Volunteer: <strong>30 points</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-eco-primary {
    color: var(--eco-primary) !important;
}

.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}

.badge.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}
</style>
@endsection
