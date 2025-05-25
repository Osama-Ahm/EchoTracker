@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('community.index') }}">Community</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
                    <li class="breadcrumb-item active">{{ $forum->name }}</li>
                </ol>
            </nav>
            <h1 class="text-eco-primary mb-2">
                <i class="{{ $forum->icon }} me-2" style="color: {{ $forum->color }}"></i>{{ $forum->name }}
            </h1>
            <p class="text-muted">{{ $forum->description }}</p>
        </div>
        <a href="{{ route('forums.create-topic', $forum) }}" class="btn btn-eco-primary">
            <i class="bi bi-plus me-2"></i>New Topic
        </a>
    </div>

    <!-- Topics List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Topics</h5>
        </div>
        <div class="card-body p-0">
            @forelse($topics as $topic)
                <div class="border-bottom p-3 {{ $topic->is_pinned ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                @if($topic->is_pinned)
                                    <span class="badge bg-warning me-2">
                                        <i class="bi bi-pin-angle"></i> Pinned
                                    </span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="badge bg-secondary me-2">
                                        <i class="bi bi-lock"></i> Locked
                                    </span>
                                @endif
                                <h6 class="mb-0">
                                    <a href="{{ route('forums.topics.show', $topic) }}" class="text-decoration-none">
                                        {{ $topic->title }}
                                    </a>
                                </h6>
                            </div>
                            <div class="text-muted small">
                                Started by <strong>{{ $topic->user->name }}</strong> 
                                • {{ $topic->created_at->diffForHumans() }}
                                @if($topic->latestReply)
                                    • Last reply by <strong>{{ $topic->latestReply->user->name }}</strong> 
                                    {{ $topic->latestReply->created_at->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-center">
                                    <div class="fw-bold text-eco-primary">{{ $topic->replies_count }}</div>
                                    <small class="text-muted">Replies</small>
                                </div>
                                <div class="text-center">
                                    <div class="fw-bold text-eco-primary">{{ $topic->views }}</div>
                                    <small class="text-muted">Views</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No topics yet</h4>
                    <p class="text-muted">Be the first to start a discussion!</p>
                    <a href="{{ route('forums.create-topic', $forum) }}" class="btn btn-eco-primary">
                        <i class="bi bi-plus me-2"></i>Create First Topic
                    </a>
                </div>
            @endforelse
        </div>
        
        @if($topics->hasPages())
            <div class="card-footer bg-white">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
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

.breadcrumb-item a {
    text-decoration: none;
    color: var(--eco-primary);
}
</style>
@endsection
