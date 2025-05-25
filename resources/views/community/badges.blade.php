@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-award me-2"></i>Community Badges
            </h1>
            <p class="text-muted">Earn badges by contributing to our environmental community</p>
        </div>
        <a href="{{ route('community.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Community
        </a>
    </div>

    <!-- User's Badges -->
    @if(Auth::user()->badges->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-star me-2"></i>Your Badges ({{ Auth::user()->badges->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(Auth::user()->badges as $userBadge)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-0 bg-light text-center h-100">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="{{ $userBadge->icon }} text-white rounded-circle p-3" 
                                           style="background-color: {{ $userBadge->color }}; font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title">{{ $userBadge->name }}</h6>
                                    <p class="card-text small text-muted">{{ $userBadge->description }}</p>
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Earned {{ $userBadge->pivot->earned_at ? \Carbon\Carbon::parse($userBadge->pivot->earned_at)->diffForHumans() : 'recently' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- All Available Badges -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-collection me-2"></i>All Available Badges
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($badges as $badge)
                    @php
                        $userHasBadge = Auth::user()->badges->contains($badge->id);
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 shadow-sm text-center h-100 {{ $userHasBadge ? 'border-success' : '' }}">
                            <div class="card-body">
                                <div class="position-relative mb-3">
                                    <div class="badge-icon mx-auto d-flex align-items-center justify-content-center rounded-circle {{ $userHasBadge ? '' : 'opacity-50' }}" 
                                         style="width: 80px; height: 80px; background-color: {{ $badge->color }};">
                                        <i class="{{ $badge->icon }} text-white" style="font-size: 2.5rem;"></i>
                                    </div>
                                    @if($userHasBadge)
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-success rounded-pill">
                                                <i class="bi bi-check"></i>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <h6 class="card-title {{ $userHasBadge ? 'text-success' : '' }}">
                                    {{ $badge->name }}
                                </h6>
                                <p class="card-text small text-muted mb-3">{{ $badge->description }}</p>
                                
                                @if($badge->points_required > 0)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-star me-1"></i>
                                            {{ number_format($badge->points_required) }} points required
                                        </small>
                                    </div>
                                @endif
                                
                                @if($userHasBadge)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Earned
                                    </span>
                                @else
                                    @php
                                        $userPoints = Auth::user()->total_points;
                                        $progress = $badge->points_required > 0 ? min(100, ($userPoints / $badge->points_required) * 100) : 0;
                                    @endphp
                                    
                                    @if($badge->points_required > 0 && $userPoints < $badge->points_required)
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-eco-primary" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <small class="text-muted">
                                            {{ number_format($userPoints) }}/{{ number_format($badge->points_required) }} points
                                        </small>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock me-1"></i>In Progress
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No badges available</h4>
                            <p class="text-muted">Badges will be available soon!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Badge Categories Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>How to Earn Badges
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-eco-primary">Reporting Badges</h6>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-1"><small>• Report your first incident</small></li>
                                <li class="mb-1"><small>• Report multiple incidents</small></li>
                                <li class="mb-1"><small>• Have incidents resolved</small></li>
                            </ul>
                            
                            <h6 class="text-eco-primary">Community Badges</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1"><small>• Participate in forum discussions</small></li>
                                <li class="mb-1"><small>• Start forum topics</small></li>
                                <li class="mb-1"><small>• Help other community members</small></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-eco-primary">Event Badges</h6>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-1"><small>• Organize community events</small></li>
                                <li class="mb-1"><small>• Attend multiple events</small></li>
                                <li class="mb-1"><small>• Complete event activities</small></li>
                            </ul>
                            
                            <h6 class="text-eco-primary">Volunteer Badges</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1"><small>• Apply for volunteer opportunities</small></li>
                                <li class="mb-1"><small>• Complete volunteer work</small></li>
                                <li class="mb-1"><small>• Create volunteer opportunities</small></li>
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

.progress-bar.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}

.badge-icon {
    transition: all 0.3s ease;
}

.card:hover .badge-icon {
    transform: scale(1.1);
}
</style>
@endsection
