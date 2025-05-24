@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-people me-2"></i>Manage Users
                    </h1>
                    <p class="text-muted">Monitor and manage all platform users</p>
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
                    <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by name or email...">
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">All Roles</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-eco-primary me-2">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
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
                <strong>{{ $users->total() }}</strong> users found
                @if(request()->hasAny(['search', 'role']))
                    with current filters
                @endif
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Reports</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person-fill text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                        @if($user->email_verified_at)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle"></i> Verified
                                                            </small>
                                                        @else
                                                            <small class="text-warning">
                                                                <i class="bi bi-exclamation-circle"></i> Unverified
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $user->email }}
                                                    @if($user->id === Auth::id())
                                                        <br><small class="text-primary">(You)</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->id !== Auth::id())
                                                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="role" class="form-select form-select-sm" 
                                                                onchange="this.form.submit()" style="min-width: 100px;">
                                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    <span class="badge bg-danger">Admin (You)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="badge bg-eco-primary">{{ $user->incidents_count }}</span>
                                                    <br><small class="text-muted">reports</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $user->created_at->format('M j, Y') }}
                                                    <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    @if($user->incidents_count > 0)
                                                        <a href="{{ route('admin.incidents') }}?search={{ urlencode($user->name) }}" 
                                                           class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-list-ul"></i> Reports
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- User Details Modal -->
                                        <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">User Details - {{ $user->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Basic Information</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Name:</strong></td>
                                                                        <td>{{ $user->name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Email:</strong></td>
                                                                        <td>{{ $user->email }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Role:</strong></td>
                                                                        <td>
                                                                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                                                                {{ ucfirst($user->role) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Status:</strong></td>
                                                                        <td>
                                                                            @if($user->email_verified_at)
                                                                                <span class="badge bg-success">Verified</span>
                                                                            @else
                                                                                <span class="badge bg-warning">Unverified</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Joined:</strong></td>
                                                                        <td>{{ $user->created_at->format('M j, Y g:i A') }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Activity Statistics</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body text-center">
                                                                        <h3 class="text-eco-primary">{{ $user->incidents_count }}</h3>
                                                                        <p class="mb-0">Total Reports Submitted</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                @if($user->incidents_count > 0)
                                                                    <div class="mt-3">
                                                                        <h6>Recent Activity</h6>
                                                                        @php
                                                                            $recentIncidents = $user->incidents()->latest()->take(3)->get();
                                                                        @endphp
                                                                        @foreach($recentIncidents as $incident)
                                                                            <div class="d-flex align-items-center mb-2">
                                                                                <i class="{{ $incident->category->icon ?? 'bi-exclamation-circle' }} me-2" 
                                                                                   style="color: {{ $incident->category->color }}"></i>
                                                                                <div>
                                                                                    <small class="fw-bold">{{ $incident->title }}</small>
                                                                                    <br><small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        
                                                                        @if($user->incidents_count > 3)
                                                                            <a href="{{ route('admin.incidents') }}?search={{ urlencode($user->name) }}" 
                                                                               class="btn btn-outline-eco-primary btn-sm">
                                                                                View All Reports
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        @if($user->incidents_count > 0)
                                                            <a href="{{ route('admin.incidents') }}?search={{ urlencode($user->name) }}" 
                                                               class="btn btn-eco-primary">View User Reports</a>
                                                        @endif
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
                            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No users found</h4>
                            <p class="text-muted">Try adjusting your filters or check back later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->links() }}
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

.bg-eco-primary {
    background-color: var(--eco-primary) !important;
}
</style>
@endsection
