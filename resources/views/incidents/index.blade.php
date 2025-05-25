@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-list-ul me-2"></i>Environmental Reports
                    </h1>
                    <p class="text-muted">Community-reported environmental issues in your area</p>
                </div>
                @auth
                    <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary">
                        <i class="bi bi-plus-circle me-2"></i>Report Issue
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('incidents.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search incidents...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-eco-primary me-2">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">{{ $incidents->total() }} incidents found</span>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('incidents.index', request()->query()) }}"
                       class="btn btn-outline-secondary active">
                        <i class="bi bi-grid"></i> Grid
                    </a>
                    <a href="{{ route('incidents.map', request()->query()) }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-geo-alt"></i> Map
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidents Grid -->
    <div class="row">
        @forelse($incidents as $incident)
            <div class="col-lg-4 col-md-6 mb-4">
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
                                <div class="d-flex gap-2">
                                    @if($incident->photos->count() > 0)
                                        <small class="text-muted">
                                            <i class="bi bi-camera"></i> {{ $incident->photos->count() }}
                                        </small>
                                    @endif
                                    @if($incident->evidence_count > 0)
                                        <small class="text-success">
                                            <i class="bi bi-people"></i> {{ $incident->evidence_count }} evidence
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $incident->display_name }}
                                </small>
                                <small class="text-muted">
                                    {{ $incident->created_at->diffForHumans() }}
                                </small>
                            </div>

                            @if($incident->address)
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($incident->address, 30) }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <a href="{{ route('incidents.show', $incident) }}"
                           class="btn btn-outline-eco-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No incidents found</h4>
                        <p class="text-muted">Try adjusting your filters or be the first to report an environmental issue.</p>
                        @auth
                            <a href="{{ route('incidents.create') }}" class="btn btn-eco-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Report First Issue
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-eco-primary mt-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Report Issues
                            </a>
                        @endauth
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
                    {{ $incidents->appends(request()->query())->links() }}
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
