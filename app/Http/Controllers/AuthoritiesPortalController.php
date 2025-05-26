<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Incident;
use App\Models\Authority;
use App\Models\AuthorityComment;
use App\Models\AuthorityAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthoritiesPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('authority.access');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $authority = $user->authority;
        
        // Get incidents relevant to this authority based on jurisdiction
        $incidents = Incident::with(['user', 'category', 'status'])
            ->whereIn('category_id', $authority->monitoredCategories()->select('categories.id')->pluck('categories.id'))
            ->orWhere(function($query) use ($authority) {
                $query->whereRaw("ST_Contains(ST_GeomFromText(?), location)", [$authority->jurisdiction_boundary]);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Critical issues count
        $criticalCount = Incident::whereIn('priority', ['high', 'urgent'])
            ->whereIn('category_id', $authority->monitoredCategories()->select('categories.id')->pluck('categories.id'))
            ->count();
            
        // Recent communications
        $recentComments = AuthorityComment::with(['incident', 'user'])
            ->where('authority_id', $authority->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('authorities.dashboard', compact(
            'incidents', 
            'criticalCount', 
            'recentComments',
            'authority'
        ));
    }
    
    public function incidents()
    {
        $user = Auth::user();
        $authority = $user->authority;
        
        $incidents = Incident::with(['user', 'category', 'status'])
            ->whereIn('category_id', $authority->monitoredCategories()->select('categories.id')->pluck('categories.id'))
            ->orWhere(function($query) use ($authority) {
                $query->whereRaw("ST_Contains(ST_GeomFromText(?), location)", [$authority->jurisdiction_boundary]);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('authorities.incidents.index', compact('incidents', 'authority'));
    }
    
    public function showIncident(Incident $incident)
    {
        $user = Auth::user();
        $authority = $user->authority;
        
        // Check if this authority has access to this incident
        $hasAccess = $authority->monitoredCategories()->select('categories.id')->where('categories.id', $incident->category_id)->exists() ||
                    DB::raw("ST_Contains(ST_GeomFromText('{$authority->jurisdiction_boundary}'), '{$incident->location}')");
                    
        if (!$hasAccess && !$user->isAdmin()) {
            abort(403, 'This incident is outside your jurisdiction or monitoring scope.');
        }
        
        $comments = AuthorityComment::with('user')
            ->where('incident_id', $incident->id)
            ->orderBy('created_at')
            ->get();
            
        return view('authorities.incidents.show', compact('incident', 'comments', 'authority'));
    }
    
    public function addComment(Request $request, Incident $incident)
    {
        $request->validate([
            'comment' => 'required|string|min:5|max:1000',
            'is_public' => 'boolean',
            'status_update' => 'nullable|exists:statuses,id',
        ]);
        
        $user = Auth::user();
        $authority = $user->authority;
        
        $comment = new AuthorityComment();
        $comment->incident_id = $incident->id;
        $comment->user_id = $user->id;
        $comment->authority_id = $authority->id;
        $comment->comment = $request->comment;
        $comment->is_public = $request->has('is_public') ? true : false;
        $comment->save();
        
        // Update incident status if requested
        if ($request->filled('status_update')) {
            $incident->status_id = $request->status_update;
            $incident->save();
        }
        
        return redirect()->back()->with('success', 'Your comment has been added.');
    }
    
    public function settings()
    {
        $user = Auth::user();
        $authority = $user->authority;
        $categories = \App\Models\Category::all();
        
        return view('authorities.settings', compact('authority', 'categories'));
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'monitored_categories' => 'array',
            'monitored_categories.*' => 'exists:categories,id',
            'notification_email' => 'required|email',
            'notification_preferences' => 'array',
        ]);
        
        $user = Auth::user();
        $authority = $user->authority;
        
        $authority->notification_email = $request->notification_email;
        $authority->notification_preferences = $request->notification_preferences ?? [];
        $authority->save();
        
        // Update monitored categories
        $authority->monitoredCategories()->sync($request->monitored_categories);
        
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}


