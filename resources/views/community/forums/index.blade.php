@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-eco-primary mb-2">
                <i class="bi bi-chat-dots me-2"></i>Community Forums
            </h1>
            <p class="text-muted">Join discussions about environmental issues and solutions</p>
        </div>
        <a href="{{ route('community.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Community
        </a>
    </div>

    <!-- Forums List -->
    <div class="row">
        @forelse($forums as $forum)
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="forum-icon me-3" style="color: {{ $forum->color }}">
                                <i class="{{ $forum->icon }}" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('forums.show', $forum) }}" class="text-decoration-none">
                                        {{ $forum->name }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted mb-3">{{ $forum->description }}</p>
                                
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="fw-bold text-eco-primary">{{ $forum->topics_count }}</div>
                                        <small class="text-muted">Topics</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-eco-primary">{{ $forum->replies_count }}</div>
                                        <small class="text-muted">Replies</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('forums.show', $forum) }}" class="btn btn-eco-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i>Browse Topics
                        </a>
                        <a href="{{ route('forums.create-topic', $forum) }}" class="btn btn-outline-eco-primary btn-sm">
                            <i class="bi bi-plus me-1"></i>New Topic
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No forums available</h4>
                    <p class="text-muted">Forums will be available soon!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Recent Activity -->
    @if($recentTopics->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-eco-primary">
                            <i class="bi bi-clock me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($recentTopics as $topic)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('forums.topics.show', $topic) }}" class="text-decoration-none">
                                                    {{ $topic->title }}
                                                </a>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                in <a href="{{ route('forums.show', $topic->forum) }}" class="text-decoration-none">{{ $topic->forum->name }}</a>
                                            </p>
                                            <small class="text-muted">
                                                by {{ $topic->user->name }} • {{ $topic->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-eco-primary">{{ $topic->replies_count }} replies</span>
                                            <div class="small text-muted">{{ $topic->views }} views</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
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

.btn-outline-eco-primary {
    color: var(--eco-primary);
    border-color: var(--eco-primary);
}

.btn-outline-eco-primary:hover {
    background-color: var(--eco-primary);
    border-color: var(--eco-primary);
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
