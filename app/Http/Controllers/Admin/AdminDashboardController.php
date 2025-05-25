<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IncidentStatusUpdated;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

        // Category statistics
        $categoryStats = Incident::with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->count];
            });

        // Monthly incident trends (last 6 months)
        $monthlyStats = Incident::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Priority distribution
        $priorityStats = Incident::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

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
            'priorityStats'
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
        $query = User::query();

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
}
