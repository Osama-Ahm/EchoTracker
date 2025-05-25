@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-calendar-event me-2"></i>Community Events
            </h1>
            <p class="text-muted">Join environmental events and make a difference</p>
        </div>
        <div>
            <a href="{{ route('community.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Community
            </a>
            <a href="{{ route('community.events.create') }}" class="btn btn-eco-primary">
                <i class="bi bi-plus me-2"></i>Create Event
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('community.events.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Event Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        @foreach($eventTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-eco-primary me-2">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('community.events.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events as $event)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    @if($event->image_path)
                        <img src="{{ Storage::url($event->image_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-eco-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-{{ $event->status == 'upcoming' ? 'success' : ($event->status == 'ongoing' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                            <span class="badge bg-info">{{ $eventTypes[$event->type] ?? $event->type }}</span>
                        </div>
                        
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-calendar me-2 text-eco-primary"></i>
                                <small>{{ $event->start_date->format('M j, Y g:i A') }}</small>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-geo-alt me-2 text-eco-primary"></i>
                                <small>{{ $event->location }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2 text-eco-primary"></i>
                                <small>Organized by {{ $event->organizer->name }}</small>
                            </div>
                        </div>
                        
                        @if($event->max_participants)
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-eco-primary" style="width: {{ ($event->attendees_count / $event->max_participants) * 100 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $event->attendees_count }}/{{ $event->max_participants }} participants</small>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-white">
                        <a href="{{ route('community.events.show', $event) }}" class="btn btn-eco-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        @if($event->status == 'upcoming' && !$event->is_full)
                            <button class="btn btn-outline-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i>RSVP
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No events found</h4>
                    <p class="text-muted">Be the first to organize a community event!</p>
                    <a href="{{ route('community.events.create') }}" class="btn btn-eco-primary">
                        <i class="bi bi-plus me-2"></i>Create Event
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $events->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<style>
.btn-eco-primary {
    background-color: var(--eco-primary);
    border-color: var(--eco-primary);
    color: white;
}

.btn-eco-primary:hover {
    background-color: var(--eco-dark);
    border-color: var(--eco-dark);
    color: white;
}

.text-eco-primary {
    color: var(--eco-primary) !important;
}

.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}

.progress-bar.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}
</style>
@endsection
