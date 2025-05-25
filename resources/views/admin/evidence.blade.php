@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-shield-check me-2"></i>Evidence Management
            </h1>
            <p class="text-muted">Manage community-submitted evidence for all incidents</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.evidence') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Evidence Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="comment" {{ request('type') == 'comment' ? 'selected' : '' }}>Comments</option>
                        <option value="photo" {{ request('type') == 'photo' ? 'selected' : '' }}>Photos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="verified" class="form-label">Verification Status</label>
                    <select class="form-select" id="verified" name="verified">
                        <option value="">All Status</option>
                        <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Verified</option>
                        <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Unverified</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="incident_id" class="form-label">Incident</label>
                    <select class="form-select" id="incident_id" name="incident_id">
                        <option value="">All Incidents</option>
                        @foreach($incidents as $incident)
                            <option value="{{ $incident->id }}" {{ request('incident_id') == $incident->id ? 'selected' : '' }}>
                                {{ Str::limit($incident->title, 50) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search evidence...">
                        <button type="submit" class="btn btn-eco-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Evidence List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-collection me-2"></i>Community Evidence 
                <span class="badge bg-eco-primary ms-2">{{ $evidence->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @forelse($evidence as $item)
                <div class="border-bottom p-3">
                    <div class="row align-items-start">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge {{ $item->type == 'photo' ? 'bg-info' : 'bg-secondary' }} me-2">
                                    <i class="bi bi-{{ $item->type == 'photo' ? 'image' : 'chat-dots' }} me-1"></i>
                                    {{ ucfirst($item->type) }}
                                </span>
                                @if($item->is_verified)
                                    <span class="badge bg-success me-2">
                                        <i class="bi bi-check-circle me-1"></i>Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning me-2">
                                        <i class="bi bi-clock me-1"></i>Unverified
                                    </span>
                                @endif
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                            
                            <h6 class="mb-2">
                                <a href="{{ route('incidents.show', $item->incident) }}" class="text-decoration-none">
                                    {{ $item->incident->title }}
                                </a>
                            </h6>
                            
                            <div class="mb-2">
                                <strong>Submitted by:</strong> {{ $item->user->name }}
                                <span class="text-muted">({{ $item->user->email }})</span>
                            </div>
                            
                            @if($item->type == 'comment')
                                <p class="mb-0">{{ $item->content }}</p>
                            @else
                                <div class="mb-2">
                                    <img src="{{ $item->file_url }}" class="img-thumbnail" style="max-height: 100px;" alt="Evidence photo">
                                </div>
                                @if($item->content)
                                    <p class="mb-1"><strong>Description:</strong> {{ $item->content }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="bi bi-file-earmark me-1"></i>{{ $item->file_name }} 
                                    ({{ $item->formatted_file_size }})
                                </small>
                            @endif
                        </div>
                        
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm {{ $item->is_verified ? 'btn-warning' : 'btn-success' }}" 
                                        onclick="toggleVerification({{ $item->id }}, {{ $item->is_verified ? 'false' : 'true' }})">
                                    <i class="bi bi-{{ $item->is_verified ? 'x-circle' : 'check-circle' }} me-1"></i>
                                    {{ $item->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="viewEvidence({{ $item->id }})">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteEvidence({{ $item->id }})">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-shield-exclamation text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No evidence found</h4>
                    <p class="text-muted">No community evidence has been submitted yet.</p>
                </div>
            @endforelse
        </div>
        
        @if($evidence->hasPages())
            <div class="card-footer bg-white">
                {{ $evidence->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleVerification(evidenceId, verify) {
    Swal.fire({
        title: verify ? 'Verify Evidence?' : 'Unverify Evidence?',
        text: verify ? 'This will mark the evidence as verified.' : 'This will mark the evidence as unverified.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: verify ? '#28a745' : '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: verify ? 'Yes, Verify' : 'Yes, Unverify'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/evidence/${evidenceId}/verify`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update verification status');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update verification status. Please try again.'
                });
            });
        }
    });
}

function deleteEvidence(evidenceId) {
    Swal.fire({
        title: 'Delete Evidence?',
        text: 'This action cannot be undone. The evidence will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/evidence/${evidenceId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete evidence');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete evidence. Please try again.'
                });
            });
        }
    });
}

function viewEvidence(evidenceId) {
    // Redirect to the incident page where the evidence can be viewed
    const evidenceRow = document.querySelector(`button[onclick="viewEvidence(${evidenceId})"]`).closest('.border-bottom');
    const incidentLink = evidenceRow.querySelector('a[href*="/incidents/"]');
    if (incidentLink) {
        window.open(incidentLink.href, '_blank');
    }
}
</script>

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
</style>
@endsection
