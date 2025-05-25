@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-heart me-2"></i>Volunteer Opportunities
            </h1>
            <p class="text-muted">Make a difference by volunteering for environmental causes</p>
        </div>
        <div>
            <a href="{{ route('community.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Community
            </a>
            <a href="{{ route('community.volunteer.create') }}" class="btn btn-eco-primary">
                <i class="bi bi-plus me-2"></i>Post Opportunity
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('community.volunteer.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="{{ request('location') }}" placeholder="Search by location...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-eco-primary me-2">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('community.volunteer.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Opportunities Grid -->
    <div class="row">
        @forelse($opportunities as $opportunity)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge {{ $opportunity->status_badge_class }}">
                                {{ ucfirst($opportunity->status) }}
                            </span>
                            <span class="badge {{ $opportunity->category_badge_class }}">
                                {{ $categories[$opportunity->category] ?? $opportunity->category }}
                            </span>
                        </div>
                        
                        <h5 class="card-title">{{ $opportunity->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($opportunity->description, 120) }}</p>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar me-2 text-eco-primary"></i>
                                <small>{{ $opportunity->start_date->format('M j, Y') }}</small>
                                @if($opportunity->end_date)
                                    <small> - {{ $opportunity->end_date->format('M j, Y') }}</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-geo-alt me-2 text-eco-primary"></i>
                                <small>{{ $opportunity->location }}</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-people me-2 text-eco-primary"></i>
                                <small>{{ $opportunity->approved_count }}/{{ $opportunity->volunteers_needed }} volunteers</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2 text-eco-primary"></i>
                                <small>Posted by {{ $opportunity->creator->name }}</small>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-eco-primary" 
                                 style="width: {{ ($opportunity->approved_count / $opportunity->volunteers_needed) * 100 }}%"></div>
                        </div>
                        
                        @if($opportunity->skills_required)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <strong>Skills:</strong> {{ Str::limit($opportunity->skills_required, 50) }}
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-white">
                        <a href="{{ route('community.volunteer.show', $opportunity) }}" class="btn btn-eco-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        @if($opportunity->status == 'active' && !$opportunity->is_full)
                            <button class="btn btn-outline-success btn-sm">
                                <i class="bi bi-hand-thumbs-up me-1"></i>Apply
                            </button>
                        @elseif($opportunity->is_full)
                            <span class="badge bg-warning">Full</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No volunteer opportunities found</h4>
                    <p class="text-muted">Be the first to post a volunteer opportunity!</p>
                    <a href="{{ route('community.volunteer.create') }}" class="btn btn-eco-primary">
                        <i class="bi bi-plus me-2"></i>Post Opportunity
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($opportunities->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $opportunities->appends(request()->query())->links() }}
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
