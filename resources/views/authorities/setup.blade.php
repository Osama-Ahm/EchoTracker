@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0 text-eco-primary">Authority Portal Setup</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">Complete this form to set up your authority profile and gain access to the Authorities Portal.</p>
                    
                    <form method="POST" action="{{ route('authorities.setup.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Authority Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Authority Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select type...</option>
                                <option value="government" {{ old('type') == 'government' ? 'selected' : '' }}>Government Agency</option>
                                <option value="ngo" {{ old('type') == 'ngo' ? 'selected' : '' }}>Non-Governmental Organization</option>
                                <option value="academic" {{ old('type') == 'academic' ? 'selected' : '' }}>Academic Institution</option>
                                <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>Private Organization</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="jurisdiction_name" class="form-label">Jurisdiction/Area Name</label>
                            <input type="text" class="form-control @error('jurisdiction_name') is-invalid @enderror" id="jurisdiction_name" name="jurisdiction_name" value="{{ old('jurisdiction_name') }}" required>
                            <div class="form-text">The geographic area your authority is responsible for (e.g., "City of Boston", "Greater London Area")</div>
                            @error('jurisdiction_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Official Contact Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone (Optional)</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notification_email" class="form-label">Notification Email</label>
                            <input type="email" class="form-control @error('notification_email') is-invalid @enderror" id="notification_email" name="notification_email" value="{{ old('notification_email') }}" required>
                            <div class="form-text">Where you want to receive alerts about new incidents</div>
                            @error('notification_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Monitored Incident Categories</label>
                            <div class="border rounded p-3">
                                @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="monitored_categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}" 
                                            {{ (is_array(old('monitored_categories')) && in_array($category->id, old('monitored_categories'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('monitored_categories')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-eco-primary">Submit Authority Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection