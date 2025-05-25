@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-graph-up me-2"></i>Environmental Data Analytics
                    </h1>
                    <p class="text-muted">Comprehensive analysis and insights from environmental incident data</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Timeframe Filter -->
                    <select class="form-select" id="timeframeFilter" onchange="updateTimeframe()">
                        <option value="7" {{ $timeframe == 7 ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $timeframe == 30 ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $timeframe == 90 ? 'selected' : '' }}>Last 90 days</option>
                        <option value="365" {{ $timeframe == 365 ? 'selected' : '' }}>Last year</option>
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
                            <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel Report
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-primary">{{ number_format($totalIncidents) }}</h2>
                    <p class="card-text text-muted mb-0">Total Incidents</p>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ $recentIncidents }} recent
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-success">{{ number_format($resolvedIncidents) }}</h2>
                    <p class="card-text text-muted mb-0">Resolved Issues</p>
                    <small class="text-muted">
                        {{ $totalIncidents > 0 ? round(($resolvedIncidents / $totalIncidents) * 100, 1) : 0 }}% resolution rate
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-info">{{ number_format($activeUsers) }}</h2>
                    <p class="card-text text-muted mb-0">Active Reporters</p>
                    <small class="text-muted">
                        Last {{ $timeframe }} days
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="card-title text-warning">
                        {{ $resolutionTimes && $resolutionTimes->avg_days ? round($resolutionTimes->avg_days, 1) : 'N/A' }}
                    </h2>
                    <p class="card-text text-muted mb-0">Avg Resolution Time</p>
                    <small class="text-muted">
                        Days to resolve
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Incident Trends -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-graph-up me-2"></i>Incident Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="incidentTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Status Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-pie-chart me-2"></i>Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <!-- Category Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-tags me-2"></i>Category Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="150"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Priority Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-exclamation-circle me-2"></i>Priority Levels
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-calendar3 me-2"></i>Monthly Trends (Last 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Data -->
    <div class="row mb-4">
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
        
        <!-- User Engagement -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-person-check me-2"></i>User Engagement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-eco-primary">{{ $userEngagement['active_reporters'] }}</h3>
                        <p class="text-muted mb-0">Active Reporters</p>
                        <small class="text-muted">Last {{ $timeframe }} days</small>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Reports:</span>
                        <strong>{{ $userEngagement['total_reports'] }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg per User:</span>
                        <strong>{{ round($userEngagement['avg_reports_per_user'], 1) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js configuration
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6c757d';

// Incident Trends Chart
const incidentTrendsCtx = document.getElementById('incidentTrendsChart').getContext('2d');
new Chart(incidentTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($incidentTrends->pluck('date')) !!},
        datasets: [{
            label: 'Incidents',
            data: {!! json_encode($incidentTrends->pluck('count')) !!},
            borderColor: '#2d5a27',
            backgroundColor: 'rgba(45, 90, 39, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusDistribution->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($statusDistribution->pluck('count')) !!},
            backgroundColor: [
                '#ffc107', // warning - reported
                '#17a2b8', // info - under_review  
                '#007bff', // primary - in_progress
                '#28a745', // success - resolved
                '#6c757d'  // secondary - closed
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($categoryDistribution->pluck('name')) !!},
        datasets: [{
            label: 'Incidents',
            data: {!! json_encode($categoryDistribution->pluck('count')) !!},
            backgroundColor: {!! json_encode($categoryDistribution->pluck('color')) !!},
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
new Chart(priorityCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($priorityDistribution->pluck('priority')) !!},
        datasets: [{
            label: 'Incidents',
            data: {!! json_encode($priorityDistribution->pluck('count')) !!},
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

// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyTrends->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Incidents',
            data: {!! json_encode($monthlyTrends->pluck('count')) !!},
            borderColor: '#2d5a27',
            backgroundColor: 'rgba(45, 90, 39, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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
    window.location.href = `{{ route('analytics.index') }}?timeframe=${timeframe}`;
}

function exportReport(format) {
    const timeframe = document.getElementById('timeframeFilter').value;
    window.location.href = `{{ route('analytics.export') }}?format=${format}&timeframe=${timeframe}`;
}
</script>

<style>
.bg-eco-primary {
    background-color: #2d5a27 !important;
}

.text-eco-primary {
    color: #2d5a27 !important;
}

.btn-eco-primary {
    background-color: #2d5a27;
    border-color: #2d5a27;
    color: white;
}

.btn-eco-primary:hover {
    background-color: #1a3a17;
    border-color: #1a3a17;
    color: white;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection
