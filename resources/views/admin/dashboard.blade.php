@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Admin Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="text-eco-primary mb-2 animate-fade-in">
                    <i class="bi bi-shield-check me-2"></i>
                    Admin Dashboard
                </h1>
                <p class="text-muted mb-0 animate-fade-in">Comprehensive platform management and analytics</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-primary">{{ $totalUsers }}</h2>
                    <p class="card-text text-muted mb-0">Total Users</p>
                    <small class="text-muted">{{ $adminUsers }} admins</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-warning">{{ $totalIncidents }}</h2>
                    <p class="card-text text-muted mb-0">Total Reports</p>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-success">{{ $statusStats['resolved'] ?? 0 }}</h2>
                    <p class="card-text text-muted mb-0">Resolved</p>
                    <small class="text-muted">{{ $statusStats['in_progress'] ?? 0 }} in progress</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-tags-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-info">{{ $totalCategories }}</h2>
                    <p class="card-text text-muted mb-0">Categories</p>
                    <small class="text-muted">Active types</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-clock-history me-2"></i>Recent Incident Reports
                    </h5>
                    <a href="{{ route('admin.incidents') }}" class="btn btn-outline-eco-primary btn-sm">
                        Manage All
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
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none text-dark">
                                                    {{ $incident->title }}
                                                </a>
                                            </h6>
                                            <p class="text-muted mb-2 small">{{ Str::limit($incident->description, 80) }}</p>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $incident->status_badge_class }} small">{{ ucfirst(str_replace('_', ' ', $incident->status)) }}</span>
                                                <span class="badge {{ $incident->priority_badge_class }} small">{{ ucfirst($incident->priority) }}</span>
                                                <small class="text-muted">by {{ $incident->display_name }}</small>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No incidents reported yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions & Stats -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-lightning me-2"></i>Admin Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.incidents') }}" class="btn btn-eco-primary">
                            <i class="bi bi-exclamation-triangle me-2"></i>Manage Reports
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-eco-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <a href="{{ route('incidents.map') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-geo-alt me-2"></i>View Map
                        </a>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>Browse Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-pie-chart me-2"></i>Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if(array_sum($statusStats) > 0)
                        @foreach($statusStats as $status => $count)
                            @php
                                $percentage = ($count / array_sum($statusStats)) * 100;
                                $badgeClass = match($status) {
                                    'reported' => 'bg-warning',
                                    'under_review' => 'bg-info',
                                    'in_progress' => 'bg-primary',
                                    'resolved' => 'bg-success',
                                    'closed' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $badgeClass }} small">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                <span class="fw-bold small">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress mb-3" style="height: 4px;">
                                <div class="progress-bar {{ str_replace('bg-', '', $badgeClass) }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center small">No data available</p>
                    @endif
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-bar-chart me-2"></i>Priority Levels
                    </h6>
                </div>
                <div class="card-body">
                    @if(array_sum($priorityStats) > 0)
                        @foreach($priorityStats as $priority => $count)
                            @php
                                $badgeClass = match($priority) {
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning',
                                    'high' => 'bg-danger',
                                    'urgent' => 'bg-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $badgeClass }} small">{{ ucfirst($priority) }}</span>
                                <span class="fw-bold small">{{ $count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center small">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-person-plus me-2"></i>Recent Users
                    </h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-eco-primary btn-sm">
                        Manage All Users
                    </a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="row">
                            @foreach($recentUsers as $user)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            <div>
                                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} small">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No users registered yet.</p>
                        </div>
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
