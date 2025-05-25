<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = CommunityEvent::with(['organizer', 'attendees']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to upcoming events
            $query->where('status', 'upcoming');
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $events = $query->orderBy('start_date')->paginate(12);
        $eventTypes = CommunityEvent::getEventTypes();

        return view('community.events.index', compact('events', 'eventTypes'));
    }

    public function show(CommunityEvent $event)
    {
        $event->load(['organizer', 'rsvps.user']);
        $userRsvp = $event->getUserRsvp(Auth::id());

        return view('community.events.show', compact('event', 'userRsvp'));
    }

    public function create()
    {
        $eventTypes = CommunityEvent::getEventTypes();
        return view('community.events.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'what_to_bring' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');
        $data['organizer_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('event-images', 'public');
        }

        $event = CommunityEvent::create($data);

        return redirect()->route('community.events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function edit(CommunityEvent $event)
    {
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own events.');
        }

        $eventTypes = CommunityEvent::getEventTypes();
        return view('community.events.edit', compact('event', 'eventTypes'));
    }

    public function update(Request $request, CommunityEvent $event)
    {
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own events.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'what_to_bring' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $data['image_path'] = $request->file('image')->store('event-images', 'public');
        }

        $event->update($data);

        return redirect()->route('community.events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(CommunityEvent $event)
    {
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only delete your own events.');
        }

        // Delete image if exists
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('community.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    public function rsvp(Request $request, CommunityEvent $event)
    {
        $request->validate([
            'status' => 'required|in:attending,maybe,not_attending',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if event is full
        if ($request->status === 'attending' && $event->is_full) {
            return back()->with('error', 'This event is already full.');
        }

        $rsvp = EventRsvp::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => $request->status,
                'notes' => $request->notes,
            ]
        );

        $statusText = match($request->status) {
            'attending' => 'attending',
            'maybe' => 'marked as maybe',
            'not_attending' => 'marked as not attending',
        };

        return back()->with('success', "You have RSVP'd as {$statusText} for this event!");
    }

    public function cancelRsvp(CommunityEvent $event)
    {
        $rsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($rsvp) {
            $rsvp->delete();
            return back()->with('success', 'Your RSVP has been cancelled.');
        }

        return back()->with('error', 'No RSVP found to cancel.');
    }

    public function myEvents()
    {
        $organizedEvents = Auth::user()->organizedEvents()
            ->withCount('attendees')
            ->orderBy('start_date')
            ->get();

        $attendingEvents = Auth::user()->attendedEvents()
            ->withPivot('status', 'notes')
            ->orderBy('start_date')
            ->get();

        return view('community.events.my-events', compact('organizedEvents', 'attendingEvents'));
    }

    // Admin functions
    public function updateStatus(Request $request, CommunityEvent $event)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Event status updated successfully!');
    }

    public function markAttendance(Request $request, CommunityEvent $event)
    {
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'attendees' => 'required|array',
            'attendees.*' => 'exists:users,id',
        ]);

        // Mark selected users as attended
        EventRsvp::where('event_id', $event->id)
            ->whereIn('user_id', $request->attendees)
            ->update(['attended' => true]);

        return back()->with('success', 'Attendance marked successfully!');
    }
}
