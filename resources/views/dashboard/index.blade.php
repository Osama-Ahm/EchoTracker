@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="text-eco-primary mb-2 animate-fade-in">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Welcome back, {{ Auth::user()->name }}!
                </h1>
                <p class="text-muted mb-0 animate-fade-in">Here's your environmental impact dashboard</p>
            </div>
        </div>
    </div>

    <!-- Personal Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-eco-primary mb-2">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-eco-primary">{{ $myIncidents }}</h2>
                    <p class="card-text text-muted mb-0">My Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-success">{{ $statusStats['resolved'] ?? 0 }}</h2>
                    <p class="card-text text-muted mb-0">Resolved Issues</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-warning">{{ ($statusStats['reported'] ?? 0) + ($statusStats['under_review'] ?? 0) + ($statusStats['in_progress'] ?? 0) }}</h2>
                    <p class="card-text text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-globe-americas" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-info">{{ $totalIncidents }}</h2>
                    <p class="card-text text-muted mb-0">Community Total</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Community Reports -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-clock-history me-2"></i>Recent Community Reports
                    </h5>
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-eco-primary btn-sm">
                        View All Reports
                    </a>
                </div>
                <div class="card-body">
                    @if($recentIncidents->count() > 0)
                        @foreach($recentIncidents as $incident)
                            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: {{ $incident->category->color }}20;">
                                        <i class="{{ $incident->category->icon ?? 'bi-exclamation-circle' }}"
                                           style="color: {{ $incident->category->color }}; font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none text-dark">
                                            {{ $incident->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-2 small">{{ Str::limit($incident->description, 80) }}</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $incident->status_badge_class }} small">{{ ucfirst(str_replace('_', ' ', $incident->status)) }}</span>
                                        <span class="badge bg-light text-dark small">{{ $incident->category->name }}</span>
                                        <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-globe text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">No community reports yet</h6>
                            <p class="text-muted">Check back later for community environmental reports!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Navigation -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->role !== 'admin')
                            <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary">
                                <i class="bi bi-plus-circle me-2"></i>Report Issue
                            </a>
                        @endif
                        <a href="{{ route('incidents.my') }}" class="btn {{ Auth::user()->role === 'admin' ? 'btn-eco-primary' : 'btn-outline-eco-primary' }}">
                            <i class="bi bi-file-earmark-text me-2"></i>My Reports
                        </a>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>Browse All Reports
                        </a>
                        <a href="{{ route('incidents.map') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-geo-alt me-2"></i>View Map
                        </a>
                    </div>
                </div>
            </div>

            <!-- Personal Impact -->
            @if($myIncidents > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 text-eco-primary">
                            <i class="bi bi-award me-2"></i>Your Environmental Impact
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-eco-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-leaf text-eco-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="text-eco-primary">{{ $myIncidents }} Report{{ $myIncidents > 1 ? 's' : '' }}</h5>
                            <p class="text-muted small mb-3">You've helped make your community cleaner!</p>

                            @if(($statusStats['resolved'] ?? 0) > 0)
                                <div class="alert alert-success py-2">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>{{ $statusStats['resolved'] }}</strong> of your reports have been resolved!
                                </div>
                            @endif

                            <p class="text-muted small">
                                Keep up the great work protecting our environment!
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-seedling text-eco-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-eco-primary">Start Your Environmental Journey</h6>
                        <p class="text-muted small mb-3">
                            Start exploring environmental reports in your community and see how you can make a difference.
                        </p>
                        <a href="{{ route('incidents.index') }}" class="btn btn-eco-primary btn-sm">
                            <i class="bi bi-eye me-2"></i>Explore Reports
                        </a>
                    </div>
                </div>
            @endif
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

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
