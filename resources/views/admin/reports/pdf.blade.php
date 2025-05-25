<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Environmental Report - {{ $report_date }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2d5a27;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2d5a27;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            color: #dc3545;
            font-weight: bold;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section h2 {
            color: #2d5a27;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metric-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #2d5a27;
            display: block;
        }
        
        .metric-label {
            color: #666;
            font-size: 10px;
            margin-top: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #2d5a27;
            color: white;
            font-weight: bold;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .admin-highlight {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .critical-alert {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        
        .status-reported { background-color: #ffc107; color: #000; }
        .status-under-review { background-color: #17a2b8; }
        .status-in-progress { background-color: #007bff; }
        .status-resolved { background-color: #28a745; }
        .status-closed { background-color: #6c757d; }
        
        .priority-low { color: #28a745; }
        .priority-medium { color: #ffc107; }
        .priority-high { color: #dc3545; }
        .priority-urgent { color: #343a40; font-weight: bold; }
        
        .admin-section {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🛡️ EcoTracker Admin Environmental Report</h1>
        <div class="subtitle">ADMINISTRATIVE DASHBOARD ANALYTICS</div>
        <p><strong>Report Generated:</strong> {{ $report_date }}</p>
        <p><strong>Analysis Period:</strong> {{ $start_date }} - {{ $report_date }} ({{ $timeframe }} days)</p>
        <p><strong>Access Level:</strong> Administrator</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <h2>Administrative Executive Summary</h2>
        <div class="admin-highlight">
            <p><strong>This administrative report provides comprehensive platform analytics and management insights for EcoTracker environmental monitoring system.</strong></p>
            <p>Platform Status: {{ $total_incidents }} total incidents managed, {{ $resolution_rate }}% resolution rate achieved. {{ $total_users }} registered users with {{ $admin_users }} administrators overseeing operations.</p>
        </div>
        
        <div class="metrics-grid">
            <div class="metric-item">
                <span class="metric-value">{{ number_format($total_users) }}</span>
                <div class="metric-label">Total Users</div>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($total_incidents) }}</span>
                <div class="metric-label">Total Incidents</div>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($resolved_incidents) }}</span>
                <div class="metric-label">Resolved Issues</div>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ $resolution_rate }}%</span>
                <div class="metric-label">Resolution Rate</div>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($admin_users) }}</span>
                <div class="metric-label">Admin Users</div>
            </div>
        </div>
    </div>

    <!-- Platform Management Overview -->
    <div class="section">
        <h2>Platform Management Overview</h2>
        <div class="admin-section">
            <h3>User Management Statistics</h3>
            <table class="data-table">
                <tr>
                    <td><strong>Total Registered Users</strong></td>
                    <td>{{ $total_users }}</td>
                </tr>
                <tr>
                    <td><strong>Administrator Accounts</strong></td>
                    <td>{{ $admin_users }}</td>
                </tr>
                <tr>
                    <td><strong>Regular Users</strong></td>
                    <td>{{ $total_users - $admin_users }}</td>
                </tr>
                <tr>
                    <td><strong>Active Reporters ({{ $timeframe }} days)</strong></td>
                    <td>{{ $user_engagement['active_reporters'] }}</td>
                </tr>
                <tr>
                    <td><strong>Average Reports per User</strong></td>
                    <td>{{ round($user_engagement['avg_reports_per_user'], 1) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Incident Management Analysis -->
    <div class="section">
        <h2>Incident Management Analysis</h2>
        
        <!-- Status Distribution -->
        <h3>Status Distribution</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                    <th>Management Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($status_distribution as $status)
                <tr>
                    <td>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $status['status'])) }}">
                            {{ $status['status'] }}
                        </span>
                    </td>
                    <td>{{ $status['count'] }}</td>
                    <td>{{ $total_incidents > 0 ? round(($status['count'] / $total_incidents) * 100, 1) : 0 }}%</td>
                    <td>
                        @switch(strtolower(str_replace(' ', '_', $status['status'])))
                            @case('reported')
                                <span class="priority-high">Requires Review</span>
                                @break
                            @case('under_review')
                                <span class="priority-medium">In Assessment</span>
                                @break
                            @case('in_progress')
                                <span class="priority-medium">Active Management</span>
                                @break
                            @case('resolved')
                                <span class="priority-low">Completed</span>
                                @break
                            @case('closed')
                                <span class="priority-low">Archived</span>
                                @break
                            @default
                                Standard
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Category Performance Analysis -->
    <div class="section">
        <h2>Environmental Category Performance</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Incidents</th>
                    <th>Percentage</th>
                    <th>Resource Allocation Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_distribution as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->count }}</td>
                    <td>{{ $total_incidents > 0 ? round(($category->count / $total_incidents) * 100, 1) : 0 }}%</td>
                    <td>
                        @if($category->count > ($total_incidents * 0.25))
                            <span class="priority-urgent">Critical Focus</span>
                        @elseif($category->count > ($total_incidents * 0.15))
                            <span class="priority-high">High Priority</span>
                        @elseif($category->count > ($total_incidents * 0.05))
                            <span class="priority-medium">Medium Priority</span>
                        @else
                            <span class="priority-low">Standard Monitoring</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Geographic Distribution -->
    <div class="section">
        <h2>Geographic Coverage Analysis</h2>
        <div class="admin-highlight">
            <strong>Coverage Summary:</strong> 
            Environmental incidents tracked across multiple locations, enabling comprehensive regional monitoring and resource allocation planning.
        </div>
        
        @if($geographic_data->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Incidents</th>
                    <th>Percentage</th>
                    <th>Regional Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($geographic_data as $location)
                <tr>
                    <td>{{ $location->city }}@if($location->state), {{ $location->state }}@endif</td>
                    <td>{{ $location->count }}</td>
                    <td>{{ $total_incidents > 0 ? round(($location->count / $total_incidents) * 100, 1) : 0 }}%</td>
                    <td>
                        @if($loop->index < 3)
                            <span class="priority-high">High Activity Zone</span>
                        @elseif($loop->index < 6)
                            <span class="priority-medium">Moderate Activity</span>
                        @else
                            <span class="priority-low">Standard Monitoring</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Performance Metrics -->
    <div class="section">
        <h2>Administrative Performance Metrics</h2>
        <div class="two-column">
            <div class="column">
                <h3>Resolution Performance</h3>
                @if($resolution_metrics)
                <table class="data-table">
                    <tr>
                        <td><strong>Average Resolution Time</strong></td>
                        <td>{{ round($resolution_metrics->avg_days, 1) }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Fastest Resolution</strong></td>
                        <td>{{ $resolution_metrics->min_days }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Longest Resolution</strong></td>
                        <td>{{ $resolution_metrics->max_days }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Total Resolved</strong></td>
                        <td>{{ $resolution_metrics->total_resolved }} incidents</td>
                    </tr>
                </table>
                @else
                <p>No resolution data available yet.</p>
                @endif
            </div>
            
            <div class="column">
                <h3>Platform Engagement</h3>
                <table class="data-table">
                    <tr>
                        <td><strong>Active Reporters</strong></td>
                        <td>{{ $user_engagement['active_reporters'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Recent Reports</strong></td>
                        <td>{{ $user_engagement['total_reports'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>User Engagement Rate</strong></td>
                        <td>{{ $total_users > 0 ? round(($user_engagement['active_reporters'] / $total_users) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td><strong>Platform Utilization</strong></td>
                        <td>{{ $total_incidents > 0 ? 'Active' : 'Developing' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Administrative Recommendations -->
    <div class="section">
        <h2>Administrative Recommendations</h2>
        <div class="critical-alert">
            <h3>Priority Actions Required:</h3>
            <ul>
                @if($resolution_rate < 60)
                <li><strong>Resolution Rate Improvement:</strong> Current {{ $resolution_rate }}% resolution rate requires immediate attention and process optimization.</li>
                @endif
                
                @if($user_engagement['active_reporters'] < ($total_users * 0.3))
                <li><strong>User Engagement:</strong> Only {{ round(($user_engagement['active_reporters'] / $total_users) * 100, 1) }}% user engagement - implement user activation campaigns.</li>
                @endif
                
                @if($category_distribution->first() && $category_distribution->first()->count > ($total_incidents * 0.4))
                <li><strong>Category Concentration:</strong> {{ $category_distribution->first()->name }} represents {{ round(($category_distribution->first()->count / $total_incidents) * 100, 1) }}% of incidents - requires specialized response team.</li>
                @endif
                
                <li><strong>Administrative Oversight:</strong> {{ $admin_users }} administrators managing {{ $total_incidents }} incidents - ensure adequate administrative coverage.</li>
            </ul>
        </div>
        
        <div class="admin-section">
            <h3>Strategic Recommendations:</h3>
            <ul>
                <li>Implement automated incident routing based on category and geographic data</li>
                <li>Establish performance benchmarks for resolution times by category</li>
                <li>Deploy additional administrative resources to high-activity geographic zones</li>
                <li>Create specialized response protocols for high-frequency incident categories</li>
                <li>Implement user engagement programs to increase community participation</li>
                <li>Establish regular administrative review cycles for platform optimization</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>EcoTracker Administrative Report | Generated on {{ $report_date }} | CONFIDENTIAL - ADMIN ACCESS ONLY</p>
        <p>This report contains sensitive platform management data. Distribution restricted to authorized administrators.</p>
    </div>
</body>
</html>
