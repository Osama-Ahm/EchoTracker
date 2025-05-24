@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body text-center bg-eco-light">
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-pencil me-2"></i>Edit Environmental Report
                    </h1>
                    <p class="text-muted mb-0">Update the details of your environmental issue report</p>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $incident->title) }}" 
                                       placeholder="Brief description of the environmental issue" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority', $incident->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $incident->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $incident->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $incident->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $incident->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Provide detailed information about the environmental issue..." required>{{ old('description', $incident->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>Location Information
                                </h5>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address', $incident->address) }}" 
                                   placeholder="Street address or nearest landmark">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $incident->city) }}" placeholder="City">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state', $incident->state) }}" placeholder="State or Province">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" name="postal_code" value="{{ old('postal_code', $incident->postal_code) }}" placeholder="Postal Code">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude', $incident->latitude) }}" 
                                       placeholder="e.g., 40.7128" readonly>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude', $incident->longitude) }}" 
                                       placeholder="e.g., -74.0060" readonly>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-eco-primary" id="getCurrentLocation">
                                <i class="bi bi-geo-alt-fill me-2"></i>Update Current Location
                            </button>
                        </div>

                        <!-- Existing Photos -->
                        @if($incident->photos->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-eco-primary mb-3">
                                        <i class="bi bi-images me-2"></i>Current Photos
                                    </h5>
                                </div>
                            </div>

                            <div class="row mb-4">
                                @foreach($incident->photos as $photo)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="{{ Storage::url($photo->path) }}" 
                                                 class="card-img-top" style="height: 150px; object-fit: cover;" 
                                                 alt="{{ $photo->original_name }}">
                                            <div class="card-body p-2">
                                                <small class="text-muted">{{ $photo->original_name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Add New Photos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-camera me-2"></i>Add New Photos (Optional)
                                </h5>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="photos" class="form-label">Upload Additional Photos</label>
                            <input type="file" class="form-control @error('photos.*') is-invalid @enderror" 
                                   id="photos" name="photos[]" multiple accept="image/*">
                            <div class="form-text">
                                You can upload additional photos (JPEG, PNG, GIF). Maximum 5MB per file.
                            </div>
                            @error('photos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-eco-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('getCurrentLocation').addEventListener('click', function() {
    if (navigator.geolocation) {
        this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Getting Location...';
        this.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                
                // Reset button
                const btn = document.getElementById('getCurrentLocation');
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Location Updated';
                btn.classList.remove('btn-outline-eco-primary');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="bi bi-geo-alt-fill me-2"></i>Update Current Location';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-eco-primary');
                    btn.disabled = false;
                }, 2000);
            },
            function(error) {
                alert('Error getting location: ' + error.message);
                const btn = document.getElementById('getCurrentLocation');
                btn.innerHTML = '<i class="bi bi-geo-alt-fill me-2"></i>Update Current Location';
                btn.disabled = false;
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
});
</script>
@endpush
@endsection
