@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body text-center bg-eco-light">
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-plus-circle me-2"></i>Report Environmental Issue
                    </h1>
                    <p class="text-muted mb-0">Help your community by reporting environmental concerns in your area</p>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" id="incidentForm">
                        @csrf

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
                                       id="title" name="title" value="{{ old('title') }}"
                                       placeholder="Brief description of the environmental issue" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : 'selected' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            data-icon="{{ $category->icon }}"
                                            data-color="{{ $category->color }}">
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
                                      placeholder="Provide detailed information about the environmental issue, including what you observed, when it occurred, and any potential impacts..." required>{{ old('description') }}</textarea>
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
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   id="address" name="address" value="{{ old('address') }}"
                                   placeholder="Enter the full address of the incident location" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Please provide the complete address. Location is required for all reports.
                            </div>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       id="city" name="city" value="{{ old('city') }}" placeholder="City">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                       id="state" name="state" value="{{ old('state') }}" placeholder="State or Province">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                       id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="Postal Code">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', request('lat')) }}"
                                       placeholder="e.g., 40.7128" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter manually or use "Current Location" button
                                </div>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude', request('lng')) }}"
                                       placeholder="e.g., -74.0060" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Address will auto-fill when coordinates change
                                </div>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-eco-primary" id="getCurrentLocation">
                                <i class="bi bi-geo-alt-fill me-2"></i>Use Current Location
                            </button>
                            <small class="text-muted d-block mt-1">
                                Click to automatically fill coordinates with your current location
                            </small>
                        </div>

                        <!-- Interactive Map -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-map me-2"></i>Select Location on Map
                                </h5>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div id="locationMap" style="height: 400px; border-radius: 15px;"></div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Click on the map to set the incident location. You can also drag the marker to adjust the position.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Photos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-camera me-2"></i>Photos (Optional)
                                </h5>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="photos" class="form-label">Upload Photos</label>
                            <input type="file" class="form-control @error('photos.*') is-invalid @enderror"
                                   id="photos" name="photos[]" multiple accept="image/*">
                            <div class="form-text">
                                You can upload multiple photos (JPEG, PNG, GIF). Maximum 5MB per file.
                            </div>
                            @error('photos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Privacy Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-eco-primary mb-3">
                                    <i class="bi bi-shield-check me-2"></i>Privacy Options
                                </h5>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1"
                                       {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_anonymous">
                                    <strong>Report Anonymously</strong>
                                    <div class="text-muted small">
                                        Your name will not be displayed publicly with this report
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-eco-primary">
                                <i class="bi bi-check-circle me-2"></i>Submit Report
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
let map;
let marker;
let isMapInitialized = false;

// Initialize the map
function initMap() {
    if (isMapInitialized) return;

    // Default center (you can change this to your preferred default location)
    const defaultLat = 40.7128;
    const defaultLng = -74.0060;

    // Get existing coordinates if available
    const existingLat = document.getElementById('latitude').value;
    const existingLng = document.getElementById('longitude').value;

    const centerLat = existingLat ? parseFloat(existingLat) : defaultLat;
    const centerLng = existingLng ? parseFloat(existingLng) : defaultLng;

    // Initialize the map
    map = L.map('locationMap').setView([centerLat, centerLng], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Add marker if coordinates exist
    if (existingLat && existingLng) {
        marker = L.marker([centerLat, centerLng], {
            draggable: true
        }).addTo(map);

        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = e.target.getLatLng();
            updateLocationFromMap(position.lat, position.lng);
        });
    }

    // Handle map clicks
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = e.target.getLatLng();
            updateLocationFromMap(position.lat, position.lng);
        });

        // Update form fields
        updateLocationFromMap(lat, lng);
    });

    isMapInitialized = true;
}

// Update location fields from map interaction
function updateLocationFromMap(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);

    // Trigger reverse geocoding
    reverseGeocode(lat, lng);

    // Show visual feedback
    showLocationSuccess();
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure the map container is ready
    setTimeout(initMap, 100);
});

document.getElementById('getCurrentLocation').addEventListener('click', function() {
    if (navigator.geolocation) {
        this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Getting Location...';
        this.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Update map if initialized
                if (map) {
                    map.setView([lat, lng], 16); // Zoom in closer for GPS location

                    // Remove existing marker
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // Add new marker
                    marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);

                    // Handle marker drag
                    marker.on('dragend', function(e) {
                        const position = e.target.getLatLng();
                        updateLocationFromMap(position.lat, position.lng);
                    });
                }

                // Use OpenStreetMap Nominatim for reverse geocoding
                reverseGeocode(lat, lng);

                // Reset button
                const btn = document.getElementById('getCurrentLocation');
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Location Set';
                btn.classList.remove('btn-outline-eco-primary');
                btn.classList.add('btn-success');

                setTimeout(() => {
                    btn.innerHTML = '<i class="bi bi-geo-alt-fill me-2"></i>Use Current Location';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-eco-primary');
                    btn.disabled = false;
                }, 2000);
            },
            function(error) {
                let errorMessage = 'Unable to retrieve your location.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Location request timed out.';
                        break;
                }
                alert(errorMessage);

                const btn = document.getElementById('getCurrentLocation');
                btn.innerHTML = '<i class="bi bi-geo-alt-fill me-2"></i>Use Current Location';
                btn.disabled = false;
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
});

// Function to reverse geocode using OpenStreetMap Nominatim
function reverseGeocode(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

    fetch(url, {
        headers: {
            'User-Agent': 'EcoTracker Environmental Monitoring Platform'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.display_name) {
            // Set the main address field
            document.getElementById('address').value = data.display_name;

            // Parse address components if available
            if (data.address) {
                const address = data.address;

                // Fill city field
                const city = address.city || address.town || address.village || address.municipality || '';
                if (city) {
                    document.getElementById('city').value = city;
                }

                // Fill state field
                const state = address.state || address.province || address.region || '';
                if (state) {
                    document.getElementById('state').value = state;
                }

                // Fill postal code field
                const postalCode = address.postcode || '';
                if (postalCode) {
                    document.getElementById('postal_code').value = postalCode;
                }
            }

            // Show success feedback
            showLocationSuccess();
        } else {
            console.warn('No address found for the given coordinates');
            // Still show success for coordinates even if address lookup fails
            showLocationSuccess();
        }
    })
    .catch(error => {
        console.error('Error during reverse geocoding:', error);
        // Don't show error to user, just log it - coordinates are still set
        showLocationSuccess();
    });
}

function showLocationSuccess() {
    // Add a temporary success indicator to address field
    const addressField = document.getElementById('address');
    const originalClass = addressField.className;
    addressField.classList.add('is-valid');

    setTimeout(() => {
        addressField.className = originalClass;
    }, 3000);
}

// Add event listeners for manual coordinate entry
document.addEventListener('DOMContentLoaded', function() {
    const latField = document.getElementById('latitude');
    const lngField = document.getElementById('longitude');
    let geocodeTimeout;

    // Function to handle coordinate changes
    function handleCoordinateChange() {
        const lat = parseFloat(latField.value);
        const lng = parseFloat(lngField.value);

        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            // Update map if initialized
            if (map) {
                map.setView([lat, lng], 15);

                // Remove existing marker
                if (marker) {
                    map.removeLayer(marker);
                }

                // Add new marker
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                // Handle marker drag
                marker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    updateLocationFromMap(position.lat, position.lng);
                });
            }

            // Clear previous timeout
            clearTimeout(geocodeTimeout);

            // Set a timeout to avoid too many API calls while user is typing
            geocodeTimeout = setTimeout(() => {
                reverseGeocode(lat, lng);
            }, 1000); // Wait 1 second after user stops typing
        }
    }

    // Add event listeners to coordinate fields
    latField.addEventListener('input', handleCoordinateChange);
    lngField.addEventListener('input', handleCoordinateChange);
    latField.addEventListener('change', handleCoordinateChange);
    lngField.addEventListener('change', handleCoordinateChange);

    // Form validation before submit
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        const address = document.getElementById('address').value;

        if (!lat || !lng || !address) {
            e.preventDefault();
            alert('Location is required. Please provide coordinates and address before submitting.');
            return false;
        }
    });
});

// Photo preview
document.getElementById('photos').addEventListener('change', function(e) {
    // You can add photo preview functionality here
});
</script>
@endpush
@endsection
