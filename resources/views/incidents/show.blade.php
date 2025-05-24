@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge {{ $incident->status_badge_class }}">
                                    {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                </span>
                                <span class="badge {{ $incident->priority_badge_class }}">
                                    {{ ucfirst($incident->priority) }} Priority
                                </span>
                            </div>
                            <h1 class="text-eco-primary mb-2">{{ $incident->title }}</h1>
                            <div class="d-flex align-items-center text-muted">
                                <i class="{{ $incident->category->icon ?? 'bi-tag' }} me-2" 
                                   style="color: {{ $incident->category->color }}"></i>
                                <span class="me-3">{{ $incident->category->name }}</span>
                                <i class="bi bi-calendar me-1"></i>
                                <span class="me-3">{{ $incident->created_at->format('M j, Y') }}</span>
                                <i class="bi bi-person me-1"></i>
                                <span>{{ $incident->display_name }}</span>
                            </div>
                        </div>
                        @auth
                            @if(Auth::id() === $incident->user_id || Auth::user()->role === 'admin')
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('incidents.edit', $incident) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('incidents.destroy', $incident) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this incident?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Photos -->
            @if($incident->photos->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-camera me-2"></i>Photos ({{ $incident->photos->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($incident->photos as $photo)
                                <div class="col-md-6 mb-3">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($photo->path) }}" 
                                             class="img-fluid rounded shadow-sm" 
                                             alt="{{ $photo->original_name }}"
                                             style="width: 100%; height: 250px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#photoModal{{ $photo->id }}">
                                        @if($photo->caption)
                                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2 rounded-bottom">
                                                <small>{{ $photo->caption }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Photo Modal -->
                                <div class="modal fade" id="photoModal{{ $photo->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $photo->original_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ Storage::url($photo->path) }}" 
                                                     class="img-fluid" alt="{{ $photo->original_name }}">
                                                @if($photo->caption)
                                                    <p class="mt-3 text-muted">{{ $photo->caption }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text me-2"></i>Description
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;">{{ $incident->description }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($incident->admin_notes && (Auth::check() && (Auth::user()->role === 'admin' || Auth::id() === $incident->user_id)))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check me-2"></i>Administrative Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line;">{{ $incident->admin_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-2"></i>Back to All Reports
                        </a>
                        @auth
                            <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary btn-sm">
                                <i class="bi bi-plus-circle me-2"></i>Report New Issue
                            </a>
                        @endauth
                        @if($incident->latitude && $incident->longitude)
                            <a href="{{ route('incidents.map') }}?lat={{ $incident->latitude }}&lng={{ $incident->longitude }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-geo-alt me-2"></i>View on Map
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location Info -->
            @if($incident->address || $incident->latitude)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt me-2"></i>Location
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($incident->address)
                            <p class="mb-2">
                                <i class="bi bi-house me-2"></i>
                                {{ $incident->address }}
                                @if($incident->city), {{ $incident->city }}@endif
                                @if($incident->state), {{ $incident->state }}@endif
                                @if($incident->postal_code) {{ $incident->postal_code }}@endif
                            </p>
                        @endif
                        
                        @if($incident->latitude && $incident->longitude)
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-crosshair me-2"></i>
                                {{ number_format($incident->latitude, 6) }}, {{ number_format($incident->longitude, 6) }}
                            </p>
                            
                            <!-- Simple map placeholder -->
                            <div class="mt-3">
                                <div id="miniMap" style="height: 200px; background: #f8f9fa; border-radius: 8px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="bi bi-geo-alt-fill text-eco-primary" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-0 small">Location Marker</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Incident Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Incident Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge {{ $incident->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Priority</small>
                            <span class="badge {{ $incident->priority_badge_class }}">
                                {{ ucfirst($incident->priority) }}
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Category</small>
                            <div class="d-flex align-items-center">
                                <i class="{{ $incident->category->icon ?? 'bi-tag' }} me-1" 
                                   style="color: {{ $incident->category->color }}"></i>
                                <small>{{ $incident->category->name }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Reported</small>
                            <small>{{ $incident->created_at->format('M j, Y') }}</small>
                        </div>
                        @if($incident->resolved_at)
                            <div class="col-6">
                                <small class="text-muted d-block">Resolved</small>
                                <small>{{ $incident->resolved_at->format('M j, Y') }}</small>
                            </div>
                            @if($incident->resolvedBy)
                                <div class="col-6">
                                    <small class="text-muted d-block">Resolved By</small>
                                    <small>{{ $incident->resolvedBy->name }}</small>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reporter Info -->
            @if(!$incident->is_anonymous)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-person me-2"></i>Reported By
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill text-white"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $incident->user->name ?? 'Unknown User' }}</div>
                                <small class="text-muted">Community Member</small>
                            </div>
                        </div>
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
</style>
@endsection
