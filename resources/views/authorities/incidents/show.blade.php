@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('authorities.incidents') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Incidents
        </a>
    </div>

    <div class="row">
        <!-- Incident Details -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">Incident Details</h5>
                    <span class="badge {{ $incident->priority_badge }}">{{ ucfirst($incident->priority) }}</span>
                </div>
                <div class="card-body">
                    <h4>{{ $incident->title }}</h4>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge {{ $incident->category->badge_class }}">{{ $incident->category->name }}</span>
                        <span class="badge {{ $incident->status->badge_class }}">{{ $incident->status->name }}</span>
                        <span class="badge bg-secondary">ID: #{{ $incident->id }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $incident->description }}</p>
                    </div>
                    
                    @if($incident->location_description)
                    <div class="mb-4">
                        <h6 class="text-muted">Location</h6>
                        <p>{{ $incident->location_description }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <h6 class="text-muted">Reported By</h6>
                        <p>{{ $incident->user->name }} on {{ $incident->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    
                    @if($incident->evidence->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-muted">Evidence</h6>
                        <div class="row">
                            @foreach($incident->evidence as $evidence)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="{{ Storage::url($evidence->file_path) }}" class="card-img-top" alt="Evidence">
                                        <div class="card-body p-2">
                                            <small class="text-muted">{{ $evidence->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Communication Thread -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">Communication Thread</h5>
                </div>
                <div class="card-body">
                    @forelse($comments as $comment)
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle {{ $comment->user->id === Auth::id() ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">
                                        {{ $comment->user->name }}
                                        <span class="badge {{ $comment->is_public ? 'bg-success' : 'bg-secondary' }} ms-2">
                                            {{ $comment->is_public ? 'Public' : 'Internal' }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ $comment->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-chat-dots text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">No comments yet.