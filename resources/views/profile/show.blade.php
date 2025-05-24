@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-4" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h2 class="text-eco-primary mb-1">{{ $user->name }}</h2>
                                    <p class="text-muted mb-2">{{ $user->email }}</p>
                                    @if($user->location)
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $user->location }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('profile.edit') }}" class="btn btn-eco-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="card-title text-primary">{{ $totalReports }}</h3>
                    <p class="card-text text-muted mb-0">Total Reports</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="card-title text-success">{{ $resolvedReports }}</h3>
                    <p class="card-text text-muted mb-0">Resolved</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="card-title text-warning">{{ $pendingReports }}</h3>
                    <p class="card-text text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="card-title text-info">
                        {{ $totalReports > 0 ? number_format(($resolvedReports / $totalReports) * 100, 1) : 0 }}%
                    </h3>
                    <p class="card-text text-muted mb-0">Success Rate</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                    <a href="{{ route('incidents.my') }}" class="btn btn-outline-eco-primary btn-sm">
                        View All Reports
                    </a>
                </div>
                <div class="card-body">
                    @if($recentReports->count() > 0)
                        @foreach($recentReports as $report)
                            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    @if($report->photos->count() > 0)
                                        <img src="{{ Storage::url($report->photos->first()->path) }}" 
                                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;" 
                                             alt="Report photo">
                                    @else
                                        <div class="rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px; background-color: {{ $report->category->color }}20;">
                                            <i class="{{ $report->category->icon ?? 'bi-exclamation-circle' }}" 
                                               style="color: {{ $report->category->color }}; font-size: 1.5rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('incidents.show', $report) }}" class="text-decoration-none text-dark">
                                                    {{ $report->title }}
                                                </a>
                                            </h6>
                                            <p class="text-muted mb-2 small">{{ Str::limit($report->description, 100) }}</p>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $report->status_badge_class }} small">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                                                <span class="badge {{ $report->priority_badge_class }} small">{{ ucfirst($report->priority) }}</span>
                                                <span class="badge" style="background-color: {{ $report->category->color }}">{{ $report->category->name }}</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No reports yet</h5>
                            <p class="text-muted">Start making a difference by reporting environmental issues.</p>
                            @if($user->role !== 'admin')
                                <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create First Report
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Info & Stats -->
        <div class="col-lg-4">
            <!-- Bio Section -->
            @if($user->bio)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 text-eco-primary">
                            <i class="bi bi-person-lines-fill me-2"></i>About
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $user->bio }}</p>
                    </div>
                </div>
            @endif

            <!-- Contact Info -->
            @if($user->phone || $user->website)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 text-eco-primary">
                            <i class="bi bi-telephone me-2"></i>Contact
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($user->phone)
                            <p class="mb-2">
                                <i class="bi bi-telephone me-2"></i>{{ $user->phone }}
                            </p>
                        @endif
                        @if($user->website)
                            <p class="mb-0">
                                <i class="bi bi-globe me-2"></i>
                                <a href="{{ $user->website }}" target="_blank" class="text-decoration-none">
                                    {{ $user->website }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Category Breakdown -->
            @if($categoryStats->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 text-eco-primary">
                            <i class="bi bi-pie-chart me-2"></i>Report Categories
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($categoryStats as $category => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">{{ $category }}</span>
                                <span class="badge bg-eco-primary">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Account Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-info-circle me-2"></i>Account Info
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Member Since</span>
                        <span class="small text-muted">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Last Active</span>
                        <span class="small text-muted">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small">Account Type</span>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}

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
