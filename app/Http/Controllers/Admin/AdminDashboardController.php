<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IncidentStatusUpdated;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Overall statistics
        $totalUsers = User::count();
        $totalIncidents = Incident::count();
        $totalCategories = IncidentCategory::count();
        $adminUsers = User::where('role', 'admin')->count();

        // Recent activity
        $recentIncidents = Incident::with(['category', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get();

        // Status statistics
        $statusStats = Incident::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Category statistics with colors
        $categoryStats = Incident::with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category->name,
                    'count' => $item->count,
                    'color' => $item->category->color,
                    'icon' => $item->category->icon
                ];
            });

        // Monthly incident trends (last 12 months)
        $monthlyStats = Incident::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
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

        // Priority distribution
        $priorityStats = Incident::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Daily trends (last 30 days)
        $dailyTrends = Incident::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M j'),
                    'count' => $item->count
                ];
            });

        // Resolution metrics
        $resolutionMetrics = Incident::whereNotNull('resolved_at')
            ->select(
                DB::raw('AVG(DATEDIFF(resolved_at, created_at)) as avg_days'),
                DB::raw('MIN(DATEDIFF(resolved_at, created_at)) as min_days'),
                DB::raw('MAX(DATEDIFF(resolved_at, created_at)) as max_days'),
                DB::raw('COUNT(*) as total_resolved')
            )
            ->first();

        // Geographic distribution
        $geographicData = Incident::select('city', 'state', DB::raw('COUNT(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city', 'state')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // User engagement metrics
        $userEngagement = [
            'active_reporters' => User::whereHas('incidents', function($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })->count(),
            'total_reports_30_days' => Incident::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'avg_reports_per_user' => $totalIncidents > 0 ? round($totalIncidents / max(1, $totalUsers - $adminUsers), 1) : 0
        ];

        // Resolution rate
        $resolutionRate = $totalIncidents > 0 ? round((($statusStats['resolved'] ?? 0) / $totalIncidents) * 100, 1) : 0;

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalIncidents',
            'totalCategories',
            'adminUsers',
            'recentIncidents',
            'recentUsers',
            'statusStats',
            'categoryStats',
            'monthlyStats',
            'priorityStats',
            'dailyTrends',
            'resolutionMetrics',
            'geographicData',
            'userEngagement',
            'resolutionRate'
        ));
    }

    public function incidents(Request $request)
    {
        $query = Incident::with(['category', 'user', 'photos']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $incidents = $query->latest()->paginate(15);
        $categories = IncidentCategory::active()->ordered()->get();

        return view('admin.incidents', compact('incidents', 'categories'));
    }

    public function users(Request $request)
    {
        $query = User::where('role', '!=', 'admin'); // Exclude admin users

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('incidents')->latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function updateIncidentStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'status' => 'required|in:reported,under_review,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string'
        ]);

        // Store the old status for email notification
        $oldStatus = $incident->status;
        $newStatus = $request->status;

        $incident->update([
            'status' => $newStatus,
            'admin_notes' => $request->admin_notes,
            'resolved_at' => $newStatus === 'resolved' ? now() : null,
            'resolved_by' => $newStatus === 'resolved' ? Auth::id() : null,
        ]);

        // Send email notification to the incident reporter if status changed
        if ($oldStatus !== $newStatus && $incident->user && $incident->user->email) {
            try {
                Mail::to($incident->user->email)->send(
                    new IncidentStatusUpdated($incident, $oldStatus, $newStatus)
                );
            } catch (\Exception $e) {
                // Log the error but don't fail the status update
                Log::error('Failed to send status update email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Incident status updated successfully!');
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'User role updated successfully!');
    }

    public function viewIncident(Incident $incident)
    {
        $incident->load(['category', 'user', 'photos', 'resolvedBy']);

        return response()->json([
            'success' => true,
            'incident' => [
                'id' => $incident->id,
                'title' => $incident->title,
                'description' => $incident->description,
                'status' => $incident->status,
                'priority' => $incident->priority,
                'created_at' => $incident->created_at->format('M j, Y g:i A'),
                'updated_at' => $incident->updated_at->format('M j, Y g:i A'),
                'resolved_at' => $incident->resolved_at ? $incident->resolved_at->format('M j, Y g:i A') : null,
                'admin_notes' => $incident->admin_notes,
                'is_anonymous' => $incident->is_anonymous,
                'address' => $incident->address,
                'city' => $incident->city,
                'state' => $incident->state,
                'postal_code' => $incident->postal_code,
                'latitude' => $incident->latitude,
                'longitude' => $incident->longitude,
                'category' => [
                    'name' => $incident->category->name,
                    'icon' => $incident->category->icon,
                    'color' => $incident->category->color,
                ],
                'user' => $incident->is_anonymous ? null : [
                    'name' => $incident->user->name,
                    'email' => $incident->user->email,
                ],
                'resolved_by' => $incident->resolvedBy ? [
                    'name' => $incident->resolvedBy->name,
                    'email' => $incident->resolvedBy->email,
                ] : null,
                'photos' => $incident->photos->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'url' => Storage::url($photo->path),
                        'original_name' => $photo->original_name,
                        'caption' => $photo->caption,
                        'size' => $photo->formatted_size,
                    ];
                }),
            ]
        ]);
    }

    public function deleteIncident(Incident $incident)
    {
        // Delete associated photos from storage
        foreach ($incident->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $incident->delete();

        return response()->json([
            'success' => true,
            'message' => 'Incident deleted successfully!'
        ]);
    }

    public function exportReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $timeframe = $request->get('timeframe', '30');
        $startDate = Carbon::now()->subDays($timeframe);

        // Gather comprehensive data for export
        $data = [
            'report_date' => Carbon::now()->format('F j, Y'),
            'timeframe' => $timeframe,
            'start_date' => $startDate->format('F j, Y'),
            'total_incidents' => Incident::count(),
            'recent_incidents' => Incident::where('created_at', '>=', $startDate)->count(),
            'resolved_incidents' => Incident::where('status', 'resolved')->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'resolution_rate' => $this->getResolutionRate(),
            'status_distribution' => $this->getStatusDistribution(),
            'category_distribution' => $this->getCategoryDistribution(),
            'priority_distribution' => $this->getPriorityDistribution(),
            'monthly_trends' => $this->getMonthlyTrends(),
            'resolution_metrics' => $this->getResolutionMetrics(),
            'geographic_data' => $this->getGeographicData(),
            'user_engagement' => $this->getUserEngagement($timeframe),
            'recent_incidents_list' => $this->getRecentIncidentsList($timeframe),
            'top_categories' => $this->getTopCategories(),
        ];

        if ($format === 'pdf') {
            return $this->exportPDF($data);
        } elseif ($format === 'csv') {
            return $this->exportCSV($data);
        }

        return redirect()->back()->with('error', 'Invalid export format');
    }

    private function getResolutionRate()
    {
        $total = Incident::count();
        $resolved = Incident::where('status', 'resolved')->count();
        return $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
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

    private function getResolutionMetrics()
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

    private function getRecentIncidentsList($days)
    {
        $startDate = Carbon::now()->subDays($days);

        return Incident::with(['category', 'user'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
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

    private function exportPDF($data)
    {
        try {
            $pdf = Pdf::loadView('admin.reports.pdf', $data);
            $filename = 'admin-environmental-report-' . Carbon::now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Fallback to HTML view if PDF generation fails
            Log::error('PDF generation failed: ' . $e->getMessage());

            return response()->view('admin.reports.pdf', $data)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="admin-environmental-report-' . Carbon::now()->format('Y-m-d') . '.html"');
        }
    }

    private function exportCSV($data)
    {
        $filename = 'admin-environmental-data-' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Write headers
            fputcsv($file, ['EcoTracker Admin Report - ' . $data['report_date']]);
            fputcsv($file, []);

            // Summary data
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Incidents', $data['total_incidents']]);
            fputcsv($file, ['Recent Incidents (' . $data['timeframe'] . ' days)', $data['recent_incidents']]);
            fputcsv($file, ['Resolved Incidents', $data['resolved_incidents']]);
            fputcsv($file, ['Total Users', $data['total_users']]);
            fputcsv($file, ['Admin Users', $data['admin_users']]);
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
            fputcsv($file, []);

            // Geographic data
            fputcsv($file, ['Geographic Distribution']);
            fputcsv($file, ['City', 'State', 'Count']);
            foreach ($data['geographic_data'] as $location) {
                fputcsv($file, [$location->city, $location->state, $location->count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
