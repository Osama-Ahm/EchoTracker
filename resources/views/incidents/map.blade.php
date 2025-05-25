@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-geo-alt me-2"></i>Environmental Issues Map
                    </h1>
                    <p class="text-muted mb-0">Interactive map showing reported environmental issues</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('incidents.index', request()->query()) }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-grid"></i> Grid View
                    </a>
                    <a href="{{ route('incidents.map', request()->query()) }}"
                       class="btn btn-outline-secondary active">
                        <i class="bi bi-geo-alt"></i> Map View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('incidents.map') }}" class="row g-3 align-items-end" id="mapFilters">
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" onchange="updateMap()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-color="{{ $category->color }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" onchange="updateMap()">
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
                            <select class="form-select" id="priority" name="priority" onchange="updateMap()">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="timeframe" class="form-label">Time Period</label>
                            <select class="form-select" id="timeframe" name="timeframe" onchange="updateMap()">
                                <option value="">All Time</option>
                                <option value="today" {{ request('timeframe') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('timeframe') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('timeframe') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="year" {{ request('timeframe') == 'year' ? 'selected' : '' }}>This Year</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <span class="text-muted small" id="incidentCount">{{ $incidents->count() }} incidents</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container with Legend -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="map" style="height: 70vh; min-height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <!-- Map Legend -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-info-circle me-2"></i>Map Legend
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="small text-muted mb-2">Status Colors:</h6>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #dc3545;"></div>
                            <small>Reported (New)</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #ffc107;"></div>
                            <small>Under Review</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #0d6efd;"></div>
                            <small>In Progress</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #198754;"></div>
                            <small>Resolved</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #6c757d;"></div>
                            <small>Closed</small>
                        </div>
                    </div>

                    <h6 class="small text-muted mb-2">Priority Sizes:</h6>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 8px; height: 8px; background-color: #6c757d;"></div>
                            <small>Low Priority</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 10px; height: 10px; background-color: #6c757d;"></div>
                            <small>Medium Priority</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #6c757d;"></div>
                            <small>High Priority</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="rounded-circle me-2" style="width: 14px; height: 14px; background-color: #6c757d;"></div>
                            <small>Urgent Priority</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-bar-chart me-2"></i>Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <div class="text-danger fw-bold" id="reportedCount">0</div>
                            <small class="text-muted">New</small>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-warning fw-bold" id="reviewCount">0</div>
                            <small class="text-muted">Review</small>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-primary fw-bold" id="progressCount">0</div>
                            <small class="text-muted">Progress</small>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-success fw-bold" id="resolvedCount">0</div>
                            <small class="text-muted">Resolved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Map Legend
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-wrap gap-3 justify-content-md-end">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    <small>Reported</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    <small>Under Review</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    <small>In Progress</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    <small>Resolved</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize map
let map = L.map('map').setView([40.7128, -74.0060], 10);
let allIncidents = @json($incidents);
let markerLayer = L.layerGroup().addTo(map);

// Debug: Log incidents data
console.log('All incidents:', allIncidents);
console.log('Number of incidents:', allIncidents.length);

// Add tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Add a test marker to verify map is working
L.marker([40.7128, -74.0060])
    .addTo(map)
    .bindPopup('Test marker - Map is working!')
    .openPopup();

// Enhanced color mapping for status
const statusColors = {
    'reported': '#dc3545',
    'under_review': '#ffc107',
    'in_progress': '#0d6efd',
    'resolved': '#198754',
    'closed': '#6c757d'
};

// Priority size mapping
const prioritySizes = {
    'low': 15,
    'medium': 18,
    'high': 21,
    'urgent': 24
};

// Initialize map with all incidents
addIncidentMarkers(allIncidents);
updateStats(allIncidents);

function addIncidentMarkers(incidents) {
    // Clear existing markers
    markerLayer.clearLayers();
    let bounds = [];

    console.log('Adding markers for incidents:', incidents.length);

    incidents.forEach((incident, index) => {
        console.log(`Processing incident ${index + 1}:`, incident);

        if (incident.latitude && incident.longitude) {
            const lat = parseFloat(incident.latitude);
            const lng = parseFloat(incident.longitude);
            bounds.push([lat, lng]);

            console.log(`Adding marker at: ${lat}, ${lng}`);

            // Get color and size based on status and priority
            const color = statusColors[incident.status] || '#6c757d';
            const size = prioritySizes[incident.priority] || 18;

            // Create enhanced marker with priority sizing
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: ${size}px; height: ${size}px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 6px rgba(0,0,0,0.4); position: relative;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 10px; font-weight: bold;">
                        ${incident.priority === 'urgent' ? '!' : ''}
                    </div>
                </div>`,
                iconSize: [size, size],
                iconAnchor: [size/2, size/2]
            });

            // Enhanced popup content
            const popupContent = createPopupContent(incident, color);

            L.marker([lat, lng], { icon: icon })
                .addTo(markerLayer)
                .bindPopup(popupContent);
        } else {
            console.log(`Incident ${index + 1} skipped - missing coordinates:`, {
                id: incident.id,
                title: incident.title,
                latitude: incident.latitude,
                longitude: incident.longitude
            });
        }
    });

    // Fit map to show all markers
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [20, 20] });
    } else {
        // Try to get user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                map.setView([position.coords.latitude, position.coords.longitude], 13);
            });
        }
    }

    // Update incident count
    document.getElementById('incidentCount').textContent = `${incidents.length} incidents`;
}

function createPopupContent(incident, color) {
    const priorityBadge = getPriorityBadge(incident.priority);
    const categoryBadge = `<span class="badge" style="background-color: ${incident.category.color}">${incident.category.name}</span>`;

    let photoHtml = '';
    if (incident.photos && incident.photos.length > 0) {
        photoHtml = `<img src="/storage/${incident.photos[0].path}" class="img-fluid rounded mb-2" style="max-height: 100px; width: 100%;">`;
    }

    return `
        <div style="min-width: 280px; max-width: 300px;">
            ${photoHtml}
            <h6 class="mb-2">${incident.title}</h6>
            <p class="mb-2 small text-muted">${incident.description.substring(0, 120)}${incident.description.length > 120 ? '...' : ''}</p>
            <div class="mb-2">
                <span class="badge" style="background-color: ${color};">${incident.status.replace('_', ' ').toUpperCase()}</span>
                ${priorityBadge}
                ${categoryBadge}
            </div>
            ${incident.address ? `<p class="mb-2 small"><i class="bi bi-geo-alt"></i> ${incident.address}</p>` : ''}
            <div class="mb-2">
                <small class="text-muted">
                    <i class="bi bi-calendar"></i> ${new Date(incident.created_at).toLocaleDateString()}
                    <br><i class="bi bi-person"></i> ${incident.display_name}
                </small>
            </div>
            <div class="d-flex gap-1">
                <a href="/incidents/${incident.id}" class="btn btn-sm btn-outline-primary">View Details</a>
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="/admin/incidents?search=${encodeURIComponent(incident.title)}" class="btn btn-sm btn-outline-secondary">Manage</a>
                    @endif
                @endauth
            </div>
        </div>
    `;
}

function getPriorityBadge(priority) {
    const badges = {
        'low': '<span class="badge bg-success">Low</span>',
        'medium': '<span class="badge bg-warning text-dark">Medium</span>',
        'high': '<span class="badge bg-danger">High</span>',
        'urgent': '<span class="badge bg-dark">Urgent</span>'
    };
    return badges[priority] || '<span class="badge bg-secondary">Unknown</span>';
}

function updateMap() {
    const category = document.getElementById('category').value;
    const status = document.getElementById('status').value;
    const priority = document.getElementById('priority').value;
    const timeframe = document.getElementById('timeframe').value;

    let filteredIncidents = allIncidents.filter(incident => {
        let matches = true;

        if (category && incident.category_id != category) matches = false;
        if (status && incident.status !== status) matches = false;
        if (priority && incident.priority !== priority) matches = false;

        if (timeframe) {
            const incidentDate = new Date(incident.created_at);
            const now = new Date();

            switch(timeframe) {
                case 'today':
                    matches = matches && incidentDate.toDateString() === now.toDateString();
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matches = matches && incidentDate >= weekAgo;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matches = matches && incidentDate >= monthAgo;
                    break;
                case 'year':
                    const yearAgo = new Date(now.getTime() - 365 * 24 * 60 * 60 * 1000);
                    matches = matches && incidentDate >= yearAgo;
                    break;
            }
        }

        return matches;
    });

    addIncidentMarkers(filteredIncidents);
    updateStats(filteredIncidents);
}

function updateStats(incidents) {
    const stats = {
        reported: 0,
        under_review: 0,
        in_progress: 0,
        resolved: 0,
        closed: 0
    };

    incidents.forEach(incident => {
        if (stats.hasOwnProperty(incident.status)) {
            stats[incident.status]++;
        }
    });

    document.getElementById('reportedCount').textContent = stats.reported;
    document.getElementById('reviewCount').textContent = stats.under_review;
    document.getElementById('progressCount').textContent = stats.in_progress;
    document.getElementById('resolvedCount').textContent = stats.resolved;
}

function clearFilters() {
    document.getElementById('category').value = '';
    document.getElementById('status').value = '';
    document.getElementById('priority').value = '';
    document.getElementById('timeframe').value = '';
    updateMap();
}



// Regular map click for non-admin users
map.on('click', function(e) {
    @auth
        @if(Auth::user()->role !== 'admin')
            if (confirm('Would you like to report an environmental issue at this location?')) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                window.location.href = `/incidents/create?lat=${lat}&lng=${lng}`;
            }
        @endif
    @else
        if (confirm('You need to login to report an issue. Would you like to login now?')) {
            window.location.href = '/login';
        }
    @endauth
});
</script>

<style>
.custom-marker {
    background: none !important;
    border: none !important;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

.leaflet-popup-content {
    margin: 12px;
}

.form-label-sm {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

/* Responsive map */
@media (max-width: 768px) {
    #map {
        height: 50vh !important;
        min-height: 400px !important;
    }
}
</style>
@endpush
@endsection
