@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-exclamation-triangle me-2"></i>Manage Incident Reports
                    </h1>
                    <p class="text-muted">Monitor and manage all environmental incident reports</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.incidents') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search incidents or users...">
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
                            <a href="{{ route('admin.incidents') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>{{ $incidents->total() }}</strong> incidents found
                @if(request()->hasAny(['search', 'category', 'status', 'priority']))
                    with current filters
                @endif
            </div>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($incidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Incident</th>
                                        <th>Reporter</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidents as $incident)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    @if($incident->photos->count() > 0)
                                                        <img src="{{ Storage::url($incident->photos->first()->path) }}"
                                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;"
                                                             alt="Incident photo">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                                                             style="width: 50px; height: 50px;">
                                                            <i class="{{ $incident->category->icon ?? 'bi-exclamation-circle' }} text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('incidents.show', $incident) }}"
                                                               class="text-decoration-none">{{ $incident->title }}</a>
                                                        </h6>
                                                        <p class="text-muted small mb-0">{{ Str::limit($incident->description, 60) }}</p>
                                                        @if($incident->address)
                                                            <small class="text-muted">
                                                                <i class="bi bi-geo-alt"></i> {{ Str::limit($incident->address, 30) }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $incident->display_name }}</strong>
                                                    @if(!$incident->is_anonymous && $incident->user)
                                                        <br><small class="text-muted">{{ $incident->user->email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $incident->category->color }};">
                                                    {{ $incident->category->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.incidents.update-status', $incident) }}"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm"
                                                            onchange="this.form.submit()" style="min-width: 120px;">
                                                        <option value="reported" {{ $incident->status == 'reported' ? 'selected' : '' }}>Reported</option>
                                                        <option value="under_review" {{ $incident->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                                        <option value="in_progress" {{ $incident->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                        <option value="closed" {{ $incident->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <span class="badge {{ $incident->priority_badge_class }}">
                                                    {{ ucfirst($incident->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $incident->created_at->format('M j, Y') }}
                                                    <br><small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('incidents.show', $incident) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#notesModal{{ $incident->id }}">
                                                        <i class="bi bi-chat-text"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $incident->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Notes Modal -->
                                        <div class="modal fade" id="notesModal{{ $incident->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('admin.incidents.update-status', $incident) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Admin Notes - {{ $incident->title }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="status" value="{{ $incident->status }}">
                                                            <div class="mb-3">
                                                                <label for="admin_notes" class="form-label">Admin Notes</label>
                                                                <textarea class="form-control" id="admin_notes" name="admin_notes"
                                                                          rows="4" placeholder="Add administrative notes...">{{ $incident->admin_notes }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-eco-primary">Save Notes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $incident->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Delete Incident</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this incident report?</p>
                                                        <div class="alert alert-warning">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                            <strong>{{ $incident->title }}</strong><br>
                                                            This action cannot be undone. All associated photos and data will be permanently deleted.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="{{ route('admin.incidents.delete', $incident) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="bi bi-trash me-2"></i>Delete Permanently
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No incidents found</h4>
                            <p class="text-muted">Try adjusting your filters or check back later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--eco-dark);
}

.table td {
    vertical-align: middle;
}
</style>
@endsection
