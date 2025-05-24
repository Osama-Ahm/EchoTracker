@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-file-earmark-text me-2"></i>My Environmental Reports
                    </h1>
                    <p class="text-muted">
                        @if(Auth::user()->role === 'admin')
                            As an admin, you can view and manage all community reports
                        @else
                            Track the status of your reported environmental issues
                        @endif
                    </p>
                </div>
                @if(Auth::user()->role !== 'admin')
                    <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary">
                        <i class="bi bi-plus-circle me-2"></i>Report New Issue
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-eco-primary mb-2">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="card-title">{{ $incidents->total() }}</h3>
                    <p class="card-text text-muted">Total Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="card-title">{{ $incidents->where('status', 'reported')->count() + $incidents->where('status', 'under_review')->count() + $incidents->where('status', 'in_progress')->count() }}</h3>
                    <p class="card-text text-muted">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="card-title">{{ $incidents->where('status', 'resolved')->count() }}</h3>
                    <p class="card-text text-muted">Resolved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-secondary mb-2">
                        <i class="bi bi-archive-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="card-title">{{ $incidents->where('status', 'closed')->count() }}</h3>
                    <p class="card-text text-muted">Closed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="row">
        @forelse($incidents as $incident)
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    @if($incident->photos->count() > 0)
                        <img src="{{ Storage::url($incident->photos->first()->path) }}"
                             class="card-img-top" style="height: 200px; object-fit: cover;"
                             alt="{{ $incident->title }}">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <i class="{{ $incident->category->icon ?? 'bi-exclamation-circle' }} text-muted"
                               style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $incident->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                            </span>
                            <span class="badge {{ $incident->priority_badge_class }}">
                                {{ ucfirst($incident->priority) }}
                            </span>
                        </div>

                        <h5 class="card-title">
                            <a href="{{ route('incidents.show', $incident) }}"
                               class="text-decoration-none text-dark">
                                {{ $incident->title }}
                            </a>
                        </h5>

                        <p class="card-text text-muted flex-grow-1">
                            {{ Str::limit($incident->description, 100) }}
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="{{ $incident->category->icon ?? 'bi-tag' }} me-1"
                                       style="color: {{ $incident->category->color }}"></i>
                                    <small class="text-muted">{{ $incident->category->name }}</small>
                                </div>
                                @if($incident->photos->count() > 0)
                                    <small class="text-muted">
                                        <i class="bi bi-camera"></i> {{ $incident->photos->count() }}
                                    </small>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ $incident->created_at->format('M j, Y') }}
                                </small>
                                <small class="text-muted">
                                    {{ $incident->created_at->diffForHumans() }}
                                </small>
                            </div>

                            @if($incident->address)
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($incident->address, 40) }}
                                    </small>
                                </div>
                            @endif

                            @if($incident->resolved_at)
                                <div class="mb-2">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle"></i> Resolved {{ $incident->resolved_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="{{ route('incidents.show', $incident) }}"
                               class="btn btn-outline-eco-primary btn-sm flex-fill">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('incidents.edit', $incident) }}"
                               class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incidents.show', $incident) }}">
                                            <i class="bi bi-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incidents.edit', $incident) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit Report
                                        </a>
                                    </li>
                                    @if($incident->latitude && $incident->longitude)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('incidents.map') }}?lat={{ $incident->latitude }}&lng={{ $incident->longitude }}">
                                                <i class="bi bi-geo-alt me-2"></i>View on Map
                                            </a>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this report?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Delete Report
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        @if(Auth::user()->role === 'admin')
                            <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Admin Account</h4>
                            <p class="text-muted">As an admin, you cannot create incident reports. You can manage all community reports instead.</p>
                            <a href="{{ route('admin.incidents') }}" class="btn btn-eco-primary mt-3">
                                <i class="bi bi-gear me-2"></i>Manage All Reports
                            </a>
                        @else
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No reports yet</h4>
                            <p class="text-muted">You haven't reported any environmental issues yet. Help your community by reporting the first one!</p>
                            <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Report Your First Issue
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($incidents->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $incidents->links() }}
                </div>
            </div>
        </div>
    @endif
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

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
</style>
@endsection
