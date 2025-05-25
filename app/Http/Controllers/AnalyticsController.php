<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $timeframe = $request->get('timeframe', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($timeframe);
        
        // Basic statistics
        $totalIncidents = Incident::count();
        $recentIncidents = Incident::where('created_at', '>=', $startDate)->count();
        $resolvedIncidents = Incident::where('status', 'resolved')->count();
        $activeUsers = User::where('role', '!=', 'admin')
            ->whereHas('incidents', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count();

        // Trend data for charts
        $incidentTrends = $this->getIncidentTrends($timeframe);
        $statusDistribution = $this->getStatusDistribution();
        $categoryDistribution = $this->getCategoryDistribution();
        $priorityDistribution = $this->getPriorityDistribution();
        $monthlyTrends = $this->getMonthlyTrends();
        $resolutionTimes = $this->getResolutionTimes();
        $geographicData = $this->getGeographicData();
        $userEngagement = $this->getUserEngagement($timeframe);

        return view('analytics.index', compact(
            'totalIncidents',
            'recentIncidents', 
            'resolvedIncidents',
            'activeUsers',
            'incidentTrends',
            'statusDistribution',
            'categoryDistribution',
            'priorityDistribution',
            'monthlyTrends',
            'resolutionTimes',
            'geographicData',
            'userEngagement',
            'timeframe'
        ));
    }

    public function exportReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $timeframe = $request->get('timeframe', '30');
        $startDate = Carbon::now()->subDays($timeframe);

        // Gather comprehensive data
        $data = [
            'report_date' => Carbon::now()->format('F j, Y'),
            'timeframe' => $timeframe,
            'start_date' => $startDate->format('F j, Y'),
            'total_incidents' => Incident::count(),
            'recent_incidents' => Incident::where('created_at', '>=', $startDate)->count(),
            'resolved_incidents' => Incident::where('status', 'resolved')->count(),
            'resolution_rate' => $this->getResolutionRate(),
            'status_distribution' => $this->getStatusDistribution(),
            'category_distribution' => $this->getCategoryDistribution(),
            'priority_distribution' => $this->getPriorityDistribution(),
            'monthly_trends' => $this->getMonthlyTrends(),
            'top_categories' => $this->getTopCategories(),
            'resolution_times' => $this->getResolutionTimes(),
            'user_engagement' => $this->getUserEngagement($timeframe),
            'geographic_summary' => $this->getGeographicSummary(),
            'recent_incidents_list' => $this->getRecentIncidentsList($timeframe),
        ];

        if ($format === 'pdf') {
            return $this->exportPDF($data);
        } elseif ($format === 'csv') {
            return $this->exportCSV($data);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data);
        }

        return redirect()->back()->with('error', 'Invalid export format');
    }

    private function getIncidentTrends($days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Incident::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M j'),
                    'count' => $item->count
                ];
            });
    }

    private function getStatusDistribution()
    {
        return Incident::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'count' => $item->count
                ];
            });
    }

    private function getCategoryDistribution()
    {
        return Incident::join('incident_categories', 'incidents.category_id', '=', 'incident_categories.id')
            ->select('incident_categories.name', 'incident_categories.color', DB::raw('COUNT(*) as count'))
            ->groupBy('incident_categories.id', 'incident_categories.name', 'incident_categories.color')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getPriorityDistribution()
    {
        return Incident::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => ucfirst($item->priority),
                    'count' => $item->count
                ];
            });
    }

    private function getMonthlyTrends()
    {
        return Incident::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'count' => $item->count
                ];
            });
    }

    private function getResolutionTimes()
    {
        return Incident::whereNotNull('resolved_at')
            ->select(
                DB::raw('AVG(DATEDIFF(resolved_at, created_at)) as avg_days'),
                DB::raw('MIN(DATEDIFF(resolved_at, created_at)) as min_days'),
                DB::raw('MAX(DATEDIFF(resolved_at, created_at)) as max_days'),
                DB::raw('COUNT(*) as total_resolved')
            )
            ->first();
    }

    private function getGeographicData()
    {
        return Incident::select('city', 'state', DB::raw('COUNT(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city', 'state')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUserEngagement($days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return [
            'active_reporters' => User::whereHas('incidents', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'total_reports' => Incident::where('created_at', '>=', $startDate)->count(),
            'avg_reports_per_user' => Incident::where('created_at', '>=', $startDate)->count() / 
                max(1, User::whereHas('incidents', function($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })->count())
        ];
    }

    private function getResolutionRate()
    {
        $total = Incident::count();
        $resolved = Incident::where('status', 'resolved')->count();
        return $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
    }

    private function getTopCategories()
    {
        return Incident::join('incident_categories', 'incidents.category_id', '=', 'incident_categories.id')
            ->select('incident_categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('incident_categories.id', 'incident_categories.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getGeographicSummary()
    {
        return [
            'total_cities' => Incident::whereNotNull('city')->distinct('city')->count(),
            'total_states' => Incident::whereNotNull('state')->distinct('state')->count(),
            'top_locations' => $this->getGeographicData()->take(5)
        ];
    }

    private function getRecentIncidentsList($days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Incident::with(['category', 'user'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    private function exportPDF($data)
    {
        $pdf = PDF::loadView('analytics.reports.pdf', $data);
        $filename = 'environmental-report-' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportCSV($data)
    {
        $filename = 'environmental-data-' . Carbon::now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, ['Environmental Data Report - ' . $data['report_date']]);
            fputcsv($file, []);
            
            // Summary data
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Incidents', $data['total_incidents']]);
            fputcsv($file, ['Recent Incidents (' . $data['timeframe'] . ' days)', $data['recent_incidents']]);
            fputcsv($file, ['Resolved Incidents', $data['resolved_incidents']]);
            fputcsv($file, ['Resolution Rate', $data['resolution_rate'] . '%']);
            fputcsv($file, []);
            
            // Category distribution
            fputcsv($file, ['Category Distribution']);
            fputcsv($file, ['Category', 'Count']);
            foreach ($data['category_distribution'] as $category) {
                fputcsv($file, [$category->name, $category->count]);
            }
            fputcsv($file, []);
            
            // Status distribution
            fputcsv($file, ['Status Distribution']);
            fputcsv($file, ['Status', 'Count']);
            foreach ($data['status_distribution'] as $status) {
                fputcsv($file, [$status['status'], $status['count']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($data)
    {
        // This would require PhpSpreadsheet package
        // For now, return CSV with Excel-friendly format
        return $this->exportCSV($data);
    }
}
