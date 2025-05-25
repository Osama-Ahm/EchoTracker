<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Environmental Data Report - {{ $report_date }}</title>
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
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #2d5a27;
            display: block;
        }
        
        .metric-label {
            color: #666;
            font-size: 11px;
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
        
        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            margin-bottom: 15px;
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🌍 EcoTracker Environmental Data Report</h1>
        <p><strong>Report Generated:</strong> {{ $report_date }}</p>
        <p><strong>Analysis Period:</strong> {{ $start_date }} - {{ $report_date }} ({{ $timeframe }} days)</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <h2>Executive Summary</h2>
        <div class="summary-box">
            <p><strong>This report provides a comprehensive analysis of environmental incident data collected through the EcoTracker platform.</strong></p>
            <p>During the {{ $timeframe }}-day analysis period, our community reported {{ $total_incidents }} environmental incidents with a {{ $resolution_rate }}% resolution rate. The data shows {{ $recent_incidents }} new incidents were reported, demonstrating active community engagement in environmental monitoring.</p>
        </div>
        
        <div class="metrics-grid">
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
                <span class="metric-value">{{ number_format($recent_incidents) }}</span>
                <div class="metric-label">Recent Reports</div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="section">
        <h2>Incident Status Analysis</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                    <th>Description</th>
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
                                Newly reported incidents awaiting review
                                @break
                            @case('under_review')
                                Incidents currently being assessed
                                @break
                            @case('in_progress')
                                Active resolution efforts underway
                                @break
                            @case('resolved')
                                Successfully addressed incidents
                                @break
                            @case('closed')
                                Completed or archived incidents
                                @break
                            @default
                                Status information
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Category Analysis -->
    <div class="section">
        <h2>Environmental Category Breakdown</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Incidents</th>
                    <th>Percentage</th>
                    <th>Priority Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_distribution as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->count }}</td>
                    <td>{{ $total_incidents > 0 ? round(($category->count / $total_incidents) * 100, 1) : 0 }}%</td>
                    <td>
                        @if($category->count > ($total_incidents * 0.2))
                            <span class="priority-high">High Impact</span>
                        @elseif($category->count > ($total_incidents * 0.1))
                            <span class="priority-medium">Medium Impact</span>
                        @else
                            <span class="priority-low">Low Impact</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Geographic Analysis -->
    <div class="section">
        <h2>Geographic Distribution</h2>
        <div class="highlight">
            <strong>Coverage Summary:</strong> 
            Environmental incidents have been reported across {{ $geographic_summary['total_cities'] }} cities 
            in {{ $geographic_summary['total_states'] }} states, demonstrating widespread community engagement.
        </div>
        
        @if($geographic_summary['top_locations']->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Incidents</th>
                    <th>Percentage of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($geographic_summary['top_locations'] as $location)
                <tr>
                    <td>{{ $location->city }}@if($location->state), {{ $location->state }}@endif</td>
                    <td>{{ $location->count }}</td>
                    <td>{{ $total_incidents > 0 ? round(($location->count / $total_incidents) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Resolution Performance -->
    <div class="section">
        <h2>Resolution Performance Metrics</h2>
        <div class="two-column">
            <div class="column">
                <h3>Resolution Statistics</h3>
                @if($resolution_times)
                <table class="data-table">
                    <tr>
                        <td><strong>Average Resolution Time</strong></td>
                        <td>{{ round($resolution_times->avg_days, 1) }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Fastest Resolution</strong></td>
                        <td>{{ $resolution_times->min_days }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Longest Resolution</strong></td>
                        <td>{{ $resolution_times->max_days }} days</td>
                    </tr>
                    <tr>
                        <td><strong>Total Resolved</strong></td>
                        <td>{{ $resolution_times->total_resolved }} incidents</td>
                    </tr>
                </table>
                @else
                <p>No resolution data available yet.</p>
                @endif
            </div>
            
            <div class="column">
                <h3>User Engagement</h3>
                <table class="data-table">
                    <tr>
                        <td><strong>Active Reporters</strong></td>
                        <td>{{ $user_engagement['active_reporters'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Reports</strong></td>
                        <td>{{ $user_engagement['total_reports'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Avg Reports per User</strong></td>
                        <td>{{ round($user_engagement['avg_reports_per_user'], 1) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Incidents Summary -->
    <div class="section">
        <h2>Recent Incidents (Last {{ $timeframe }} Days)</h2>
        @if($recent_incidents_list->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reporter</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_incidents_list->take(15) as $incident)
                <tr>
                    <td>{{ $incident->created_at->format('M j, Y') }}</td>
                    <td>{{ $incident->category->name ?? 'N/A' }}</td>
                    <td>{{ $incident->city ?? 'Unknown' }}@if($incident->state), {{ $incident->state }}@endif</td>
                    <td>
                        <span class="status-badge status-{{ $incident->status }}">
                            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                        </span>
                    </td>
                    <td>{{ $incident->is_anonymous ? 'Anonymous' : ($incident->user->name ?? 'Unknown') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($recent_incidents_list->count() > 15)
        <p><em>Showing 15 of {{ $recent_incidents_list->count() }} recent incidents.</em></p>
        @endif
        @else
        <p>No recent incidents to display.</p>
        @endif
    </div>

    <!-- Recommendations -->
    <div class="section">
        <h2>Recommendations & Next Steps</h2>
        <div class="summary-box">
            <h3>Key Insights:</h3>
            <ul>
                @if($resolution_rate < 50)
                <li><strong>Resolution Rate Improvement:</strong> Current resolution rate of {{ $resolution_rate }}% suggests need for enhanced response protocols.</li>
                @endif
                
                @if($category_distribution->first() && $category_distribution->first()->count > ($total_incidents * 0.3))
                <li><strong>Category Focus:</strong> {{ $category_distribution->first()->name }} represents {{ round(($category_distribution->first()->count / $total_incidents) * 100, 1) }}% of incidents - consider targeted intervention.</li>
                @endif
                
                @if($user_engagement['avg_reports_per_user'] > 2)
                <li><strong>High Engagement:</strong> Users are actively reporting multiple incidents (avg {{ round($user_engagement['avg_reports_per_user'], 1) }} per user).</li>
                @endif
                
                <li><strong>Community Growth:</strong> {{ $user_engagement['active_reporters'] }} active reporters demonstrate strong community participation.</li>
            </ul>
            
            <h3>Recommended Actions:</h3>
            <ul>
                <li>Focus resources on high-frequency incident categories</li>
                <li>Improve response times for better resolution rates</li>
                <li>Expand community outreach in underrepresented areas</li>
                <li>Implement preventive measures for recurring incident types</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>EcoTracker Environmental Monitoring Platform | Generated on {{ $report_date }} | Page <span class="pagenum"></span></p>
        <p>This report contains confidential environmental data. Please handle according to your organization's data policies.</p>
    </div>
</body>
</html>
