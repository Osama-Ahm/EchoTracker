<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\IncidentPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'map']);
        $this->middleware(function ($request, $next) {
            // Only allow regular users to create/edit/delete incidents
            if (in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy'])
                && Auth::check() && Auth::user()->role === 'admin') {
                abort(403, 'Admins cannot report issues. Only regular users can create incident reports.');
            }
            return $next($request);
        })->except(['index', 'show', 'map', 'my']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['category', 'user', 'photos']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $incidents = $query->latest()->paginate(12);
        $categories = IncidentCategory::active()->ordered()->get();

        return view('incidents.index', compact('incidents', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = IncidentCategory::active()->ordered()->get();
        return view('incidents.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:incident_categories,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_anonymous' => 'boolean',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $validated['user_id'] = $request->boolean('is_anonymous') ? null : Auth::id();

        $incident = Incident::create($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('incident-photos', $filename, 'public');

                IncidentPhoto::create([
                    'incident_id' => $incident->id,
                    'filename' => $filename,
                    'original_name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $photo->getMimeType(),
                    'size' => $photo->getSize(),
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Environmental incident reported successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        $incident->load(['category', 'user', 'photos', 'resolvedBy']);
        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);
        $categories = IncidentCategory::active()->ordered()->get();
        return view('incidents.edit', compact('incident', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:incident_categories,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'priority' => 'required|in:low,medium,high,urgent',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $incident->update($validated);

        // Handle new photo uploads
        if ($request->hasFile('photos')) {
            $maxSortOrder = $incident->photos()->max('sort_order') ?? -1;

            foreach ($request->file('photos') as $index => $photo) {
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('incident-photos', $filename, 'public');

                IncidentPhoto::create([
                    'incident_id' => $incident->id,
                    'filename' => $filename,
                    'original_name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $photo->getMimeType(),
                    'size' => $photo->getSize(),
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        // Delete associated photos from storage
        foreach ($incident->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully!');
    }

    /**
     * Show incidents on a map
     */
    public function map(Request $request)
    {
        $query = Incident::with(['category', 'photos', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Apply same filters as index
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('timeframe')) {
            switch ($request->timeframe) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        $incidents = $query->get();
        $categories = IncidentCategory::active()->ordered()->get();

        return view('incidents.map', compact('incidents', 'categories'));
    }

    /**
     * Show user's own incidents
     */
    public function my()
    {
        $incidents = Incident::with(['category', 'photos'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('incidents.my', compact('incidents'));
    }
}
