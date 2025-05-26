@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-building me-2"></i>Authority Portal
            </h1>
            <p class="text-muted">{{ $authority->name }} - {{ $authority->jurisdiction_name }}</p>
        </div>
        <div>
            <a href="{{ route('authorities.settings') }}" class="btn btn-outline-secondary">
                <i class="bi bi-gear me-2"></i>Settings
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary text-white">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h5 class="ms-3 mb-0">Critical Issues</h5>
                    </div>
                    <h2 class="mb-0 mt-auto">{{ $criticalCount }}</h2>
                    <p class="text-muted">High/Urgent priority</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h5 class="ms-3 mb-0">Resolved</h5>
                    </div>
                    <h2 class="mb-0 mt-auto">{{ \App\Models\Incident::whereIn('category_id', $authority->monitoredCategories()->pluck('id'))->where('status_id', 4)->count() }}</h2>
                    <p class="text-muted">Issues marked resolved</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning text-white">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h5 class="ms-3 mb-0">Pending</h5>
                    </div>
                    <h2 class="mb-0 mt-auto">{{ \App\Models\Incident::whereIn('category_id', $authority->monitoredCategories()->pluck('id'))->where('status_id', 2)->count() }}</h2>
                    <p class="text-muted">Under investigation</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-info text-white">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h5 class="ms-3 mb-0">Communications</h5>
                    </div>
                    <h2 class="mb-0 mt-auto">{{ $recentComments->count() }}</h2>
                    <p class="text-muted">Recent responses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Incidents -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-eco-primary">
                <i class="bi bi-flag me-2"></i>Priority Incidents
            </h5>
            <a href="{{ route('authorities.incidents') }}" class="btn btn-sm btn-outline-eco-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reported</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                            <tr>
                                <td>#{{ $incident->id }}</td>
                                <td>{{ Str::limit($incident->title, 40) }}</td>
                                <td>
                                    <span class="badge {{ $incident->category->badge_class }}">
                                        {{ $incident->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $incident->priority_badge }}">
                                        {{ ucfirst($incident->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $incident->status->badge_class }}">
                                        {{ $incident->status->name }}
                                    </span>
                                </td>
                                <td>{{ $incident->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('authorities.incidents.show', $incident) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No priority incidents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Communications -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-eco-primary">
                <i class="bi bi-chat-dots me-2"></i>Recent Communications
            </h5>
        </div>
        <div class="card-body">
            @forelse($recentComments as $comment)
                <div class="d-flex mb-3 pb-3 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="icon-circle bg-light">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Re: {{ $comment->incident->title }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ Str::limit($comment->comment, 100) }}</p>
                        <div>
                            <span class="badge {{ $comment->is_public ? 'bg-success' : 'bg-secondary' }}">
                                {{ $comment->is_public ? 'Public' : 'Internal' }}
                            </span>
                            <a href="{{ route('authorities.incidents.show', $comment->incident) }}" class="btn btn-sm btn-link">View Thread</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No recent communications found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
```
</