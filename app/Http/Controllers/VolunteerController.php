<?php

namespace App\Http\Controllers;

use App\Models\VolunteerOpportunity;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = VolunteerOpportunity::with(['creator', 'applications']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active opportunities
            $query->where('status', 'active');
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by upcoming
        if ($request->filled('upcoming')) {
            $query->where('start_date', '>', now());
        }

        $opportunities = $query->orderBy('start_date')->paginate(12);
        $categories = VolunteerOpportunity::getCategories();

        return view('community.volunteer.index', compact('opportunities', 'categories'));
    }

    public function show(VolunteerOpportunity $opportunity)
    {
        $opportunity->load(['creator', 'applications.user']);
        $userApplication = $opportunity->getUserApplication(Auth::id());

        return view('community.volunteer.show', compact('opportunity', 'userApplication'));
    }

    public function create()
    {
        $categories = VolunteerOpportunity::getCategories();
        return view('community.volunteer.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'volunteers_needed' => 'required|integer|min:1',
            'skills_required' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        $opportunity = VolunteerOpportunity::create($data);

        return redirect()->route('community.volunteer.show', $opportunity)
            ->with('success', 'Volunteer opportunity created successfully!');
    }

    public function edit(VolunteerOpportunity $opportunity)
    {
        if ($opportunity->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own volunteer opportunities.');
        }

        $categories = VolunteerOpportunity::getCategories();
        return view('community.volunteer.edit', compact('opportunity', 'categories'));
    }

    public function update(Request $request, VolunteerOpportunity $opportunity)
    {
        if ($opportunity->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own volunteer opportunities.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'volunteers_needed' => 'required|integer|min:1',
            'skills_required' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
        ]);

        $opportunity->update($request->all());

        return redirect()->route('community.volunteer.show', $opportunity)
            ->with('success', 'Volunteer opportunity updated successfully!');
    }

    public function destroy(VolunteerOpportunity $opportunity)
    {
        if ($opportunity->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only delete your own volunteer opportunities.');
        }

        $opportunity->delete();

        return redirect()->route('community.volunteer.index')
            ->with('success', 'Volunteer opportunity deleted successfully!');
    }

    public function apply(Request $request, VolunteerOpportunity $opportunity)
    {
        // Check if opportunity is still active
        if ($opportunity->status !== 'active') {
            return back()->with('error', 'This volunteer opportunity is no longer active.');
        }

        // Check if already applied
        if ($opportunity->hasUserApplied(Auth::id())) {
            return back()->with('error', 'You have already applied for this opportunity.');
        }

        // Check if opportunity is full
        if ($opportunity->is_full) {
            return back()->with('error', 'This volunteer opportunity is already full.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:500',
            'availability' => 'required|string|max:500',
        ]);

        VolunteerApplication::create([
            'opportunity_id' => $opportunity->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'skills' => $request->skills,
            'availability' => $request->availability,
        ]);

        return back()->with('success', 'Your application has been submitted successfully!');
    }

    public function withdrawApplication(VolunteerOpportunity $opportunity)
    {
        $application = VolunteerApplication::where('opportunity_id', $opportunity->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($application) {
            $application->delete();
            return back()->with('success', 'Your application has been withdrawn.');
        }

        return back()->with('error', 'No application found to withdraw.');
    }

    public function myApplications()
    {
        $applications = Auth::user()->volunteerApplications()
            ->with('opportunity')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('community.volunteer.my-applications', compact('applications'));
    }

    public function myOpportunities()
    {
        $opportunities = Auth::user()->createdVolunteerOpportunities()
            ->withCount(['applications', 'approvedApplications'])
            ->orderBy('start_date')
            ->paginate(10);

        return view('community.volunteer.my-opportunities', compact('opportunities'));
    }

    public function manageApplications(VolunteerOpportunity $opportunity)
    {
        if ($opportunity->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only manage applications for your own opportunities.');
        }

        $applications = $opportunity->applications()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        return view('community.volunteer.manage-applications', compact('opportunity', 'applications'));
    }

    public function updateApplicationStatus(Request $request, VolunteerApplication $application)
    {
        $opportunity = $application->opportunity;
        
        if ($opportunity->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only manage applications for your own opportunities.');
        }

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Check if approving would exceed capacity
        if ($request->status === 'approved' && $opportunity->is_full) {
            return back()->with('error', 'Cannot approve more volunteers - opportunity is full.');
        }

        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        $statusText = match($request->status) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            'pending' => 'marked as pending',
        };

        return back()->with('success', "Application has been {$statusText}.");
    }
}
