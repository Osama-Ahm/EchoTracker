@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Welcome Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center position-relative">
                <div class="float-animation">
                    <h1 class="text-eco-primary mb-3 fw-bold" style="font-size: 2.5rem;">
                        <i class="bi bi-speedometer2 me-3 icon-hover"></i>
                        Welcome back, {{ Auth::user()->name }}!
                    </h1>
                </div>
                <p class="text-muted mb-4 fs-5">Here's your environmental impact dashboard</p>
                <div class="d-inline-block">
                    <div class="glass px-4 py-2">
                        <small class="text-eco-primary fw-semibold">
                            <i class="bi bi-calendar-check me-2"></i>
                            {{ now()->format('l, F j, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Statistics -->
    <div class="row mb-5 stagger-animation">
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-body text-center p-4">
                    <div class="position-relative">
                        <div class="bg-eco-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 float-animation"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-file-earmark-text-fill text-eco-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h2 class="card-title text-eco-primary fw-bold mb-1" style="font-size: 2.5rem;">{{ $myIncidents }}</h2>
                    <p class="card-text text-muted mb-0 fw-medium">My Reports</p>
                    <div class="mt-2">
                        <small class="text-eco-primary">
                            <i class="bi bi-arrow-up-right me-1"></i>
                            Your contributions
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-body text-center p-4">
                    <div class="position-relative">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 float-animation"
                             style="width: 80px; height: 80px; animation-delay: 0.5s;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h2 class="card-title text-success fw-bold mb-1" style="font-size: 2.5rem;">{{ $statusStats['resolved'] ?? 0 }}</h2>
                    <p class="card-text text-muted mb-0 fw-medium">Resolved Issues</p>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Completed
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-body text-center p-4">
                    <div class="position-relative">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 float-animation"
                             style="width: 80px; height: 80px; animation-delay: 1s;">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h2 class="card-title text-warning fw-bold mb-1" style="font-size: 2.5rem;">{{ ($statusStats['reported'] ?? 0) + ($statusStats['under_review'] ?? 0) + ($statusStats['in_progress'] ?? 0) }}</h2>
                    <p class="card-text text-muted mb-0 fw-medium">Pending</p>
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Active work
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-body text-center p-4">
                    <div class="position-relative">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 float-animation"
                             style="width: 80px; height: 80px; animation-delay: 1.5s;">
                            <i class="bi bi-globe-americas text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h2 class="card-title text-info fw-bold mb-1" style="font-size: 2.5rem;">{{ $totalIncidents }}</h2>
                    <p class="card-text text-muted mb-0 fw-medium">Community Total</p>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="bi bi-globe me-1"></i>
                            All reports
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Community Reports -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                    <h5 class="mb-0 text-eco-primary fw-bold">
                        <i class="bi bi-clock-history me-2 icon-hover"></i>Recent Community Reports
                    </h5>
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-eco-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i>View All Reports
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
            <div class="card border-0 glass mb-4 position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                <div class="card-header bg-transparent border-0 p-4">
                    <h6 class="mb-0 text-eco-primary fw-bold">
                        <i class="bi bi-lightning me-2 icon-hover"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        @if(Auth::user()->role !== 'admin')
                            <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary position-relative">
                                <i class="bi bi-plus-circle me-2"></i>Report Issue
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    New
                                </span>
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
                <div class="card border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h6 class="mb-0 text-eco-primary fw-bold">
                            <i class="bi bi-award me-2 icon-hover"></i>Your Environmental Impact
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-eco-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center float-animation"
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-leaf text-eco-primary" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="text-eco-primary fw-bold mb-2">{{ $myIncidents }} Report{{ $myIncidents > 1 ? 's' : '' }}</h5>
                            <p class="text-muted mb-4">You've helped make your community cleaner!</p>

                            @if(($statusStats['resolved'] ?? 0) > 0)
                                <div class="glass p-3 mb-3" style="background: rgba(25, 135, 84, 0.1);">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong class="text-success">{{ $statusStats['resolved'] }}</strong>
                                    <span class="text-muted">of your reports have been resolved!</span>
                                </div>
                            @endif

                            <p class="text-muted small mb-0">
                                <i class="bi bi-heart-fill text-danger me-1"></i>
                                Keep up the great work protecting our environment!
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 glass position-relative overflow-hidden" style="backdrop-filter: blur(20px);">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="bg-eco-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center float-animation"
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-seedling text-eco-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h6 class="text-eco-primary fw-bold mb-3">Start Your Environmental Journey</h6>
                        <p class="text-muted mb-4">
                            Start exploring environmental reports in your community and see how you can make a difference.
                        </p>
                        <a href="{{ route('incidents.index') }}" class="btn btn-eco-primary">
                            <i class="bi bi-eye me-2"></i>Explore Reports
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
/* Enhanced dashboard animations */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.page-content {
    animation: slideInUp 0.8s ease-out;
}

/* Enhanced card hover effects */
.card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 20px !important;
}

.card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

/* Glassmorphism enhancements */
.glass {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

/* Badge animations */
.badge {
    animation: pulse 2s infinite;
}

/* Icon hover effects */
.icon-hover {
    transition: all 0.3s ease;
}

.icon-hover:hover {
    transform: rotate(15deg) scale(1.2);
    filter: drop-shadow(0 4px 8px rgba(45, 90, 39, 0.3));
}

/* Button enhancements */
.btn {
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

/* Responsive enhancements */
@media (max-width: 768px) {
    .float-animation {
        animation-duration: 4s;
    }

    .card {
        margin-bottom: 1.5rem;
    }

    .stagger-animation > * {
        animation-duration: 0.4s;
    }
}

/* Loading states */
.loading {
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>
@endsection
