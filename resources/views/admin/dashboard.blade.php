@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Admin Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2 animate-fade-in">
                        <i class="bi bi-shield-check me-2"></i>
                        Admin Dashboard
                    </h1>
                    <p class="text-muted mb-0 animate-fade-in">Comprehensive platform management and analytics</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Timeframe Filter -->
                    <select class="form-select" id="timeframeFilter" onchange="updateTimeframe()">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>

                    <!-- Export Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-eco-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-2"></i>Export Report
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF Report
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportReport('csv')">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV Data
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-primary">{{ $totalUsers }}</h2>
                    <p class="card-text text-muted mb-0">Total Users</p>
                    <small class="text-muted">{{ $adminUsers }} admins</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-warning">{{ $totalIncidents }}</h2>
                    <p class="card-text text-muted mb-0">Total Reports</p>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-success">{{ $statusStats['resolved'] ?? 0 }}</h2>
                    <p class="card-text text-muted mb-0">Resolved</p>
                    <small class="text-muted">{{ $resolutionRate }}% resolution rate</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-info">
                        {{ $resolutionMetrics && $resolutionMetrics->avg_days ? round($resolutionMetrics->avg_days, 1) : 'N/A' }}
                    </h2>
                    <p class="card-text text-muted mb-0">Avg Resolution</p>
                    <small class="text-muted">Days to resolve</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="row mb-4">
        <!-- Daily Trends Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-graph-up me-2"></i>Daily Incident Trends (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-pie-chart me-2"></i>Category Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-calendar3 me-2"></i>Monthly Trends (Last 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-exclamation-triangle me-2"></i>Priority Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic and User Analytics -->
    <div class="row mb-4">
        <!-- Geographic Distribution -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-geo-alt me-2"></i>Geographic Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @if($geographicData->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Incidents</th>
                                        <th>Percentage</th>
                                        <th>Visual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($geographicData as $location)
                                        <tr>
                                            <td>
                                                <strong>{{ $location->city }}</strong>
                                                @if($location->state)
                                                    <br><small class="text-muted">{{ $location->state }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $location->count }}</td>
                                            <td>{{ round(($location->count / $totalIncidents) * 100, 1) }}%</td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-eco-primary"
                                                         style="width: {{ ($location->count / $geographicData->first()->count) * 100 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No geographic data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Engagement Metrics -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-people me-2"></i>User Engagement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-eco-primary">{{ $userEngagement['active_reporters'] }}</h3>
                        <p class="text-muted mb-0">Active Reporters</p>
                        <small class="text-muted">Last 30 days</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Reports:</span>
                        <strong>{{ $userEngagement['total_reports_30_days'] }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Avg per User:</span>
                        <strong>{{ $userEngagement['avg_reports_per_user'] }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Engagement Rate:</span>
                        <strong>{{ $totalUsers > 0 ? round(($userEngagement['active_reporters'] / $totalUsers) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-clock-history me-2"></i>Recent Incident Reports
                    </h5>
                    <a href="{{ route('admin.incidents') }}" class="btn btn-outline-eco-primary btn-sm">
                        Manage All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentIncidents->count() > 0)
                        @foreach($recentIncidents as $incident)
                            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: {{ $incident->category->color }}20;">
                                        <i class="{{ $incident->category->icon ?? 'bi-exclamation-circle' }}"
                                           style="color: {{ $incident->category->color }}; font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none text-dark">
                                                    {{ $incident->title }}
                                                </a>
                                            </h6>
                                            <p class="text-muted mb-2 small">{{ Str::limit($incident->description, 80) }}</p>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $incident->status_badge_class }} small">{{ ucfirst(str_replace('_', ' ', $incident->status)) }}</span>
                                                <span class="badge {{ $incident->priority_badge_class }} small">{{ ucfirst($incident->priority) }}</span>
                                                <small class="text-muted">by {{ $incident->display_name }}</small>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No incidents reported yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions & Stats -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-lightning me-2"></i>Admin Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.incidents') }}" class="btn btn-eco-primary">
                            <i class="bi bi-exclamation-triangle me-2"></i>Manage Reports
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-eco-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <a href="{{ route('incidents.map') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-geo-alt me-2"></i>View Map
                        </a>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>Browse Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-pie-chart me-2"></i>Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if(array_sum($statusStats) > 0)
                        @foreach($statusStats as $status => $count)
                            @php
                                $percentage = ($count / array_sum($statusStats)) * 100;
                                $badgeClass = match($status) {
                                    'reported' => 'bg-warning',
                                    'under_review' => 'bg-info',
                                    'in_progress' => 'bg-primary',
                                    'resolved' => 'bg-success',
                                    'closed' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $badgeClass }} small">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                <span class="fw-bold small">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress mb-3" style="height: 4px;">
                                <div class="progress-bar {{ str_replace('bg-', '', $badgeClass) }}"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center small">No data available</p>
                    @endif
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-bar-chart me-2"></i>Priority Levels
                    </h6>
                </div>
                <div class="card-body">
                    @if(array_sum($priorityStats) > 0)
                        @foreach($priorityStats as $priority => $count)
                            @php
                                $badgeClass = match($priority) {
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning',
                                    'high' => 'bg-danger',
                                    'urgent' => 'bg-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $badgeClass }} small">{{ ucfirst($priority) }}</span>
                                <span class="fw-bold small">{{ $count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center small">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-person-plus me-2"></i>Recent Users
                    </h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-eco-primary btn-sm">
                        Manage All Users
                    </a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="row">
                            @foreach($recentUsers as $user)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="bg-eco-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            <div>
                                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} small">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No users registered yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js configuration
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6c757d';

// Daily Trends Chart
const dailyTrendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');
new Chart(dailyTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyTrends->pluck('date')) !!},
        datasets: [{
            label: 'Daily Incidents',
            data: {!! json_encode($dailyTrends->pluck('count')) !!},
            borderColor: '#2d5a27',
            backgroundColor: 'rgba(45, 90, 39, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#2d5a27',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryStats->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($categoryStats->pluck('count')) !!},
            backgroundColor: {!! json_encode($categoryStats->pluck('color')) !!},
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyTrendsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyStats->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Incidents',
            data: {!! json_encode($monthlyStats->pluck('count')) !!},
            backgroundColor: 'rgba(45, 90, 39, 0.8)',
            borderColor: '#2d5a27',
            borderWidth: 1,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Priority Distribution Chart
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
const priorityData = {!! json_encode(array_values($priorityStats)) !!};
const priorityLabels = {!! json_encode(array_map('ucfirst', array_keys($priorityStats))) !!};

new Chart(priorityCtx, {
    type: 'bar',
    data: {
        labels: priorityLabels,
        datasets: [{
            label: 'Incidents by Priority',
            data: priorityData,
            backgroundColor: [
                '#28a745', // low - green
                '#ffc107', // medium - yellow
                '#dc3545', // high - red
                '#343a40'  // urgent - dark
            ],
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Functions
function updateTimeframe() {
    const timeframe = document.getElementById('timeframeFilter').value;
    window.location.href = `{{ route('admin.dashboard') }}?timeframe=${timeframe}`;
}

function exportReport(format) {
    const timeframe = document.getElementById('timeframeFilter').value;
    window.location.href = `{{ route('admin.export') }}?format=${format}&timeframe=${timeframe}`;
}
</script>
@endsection
