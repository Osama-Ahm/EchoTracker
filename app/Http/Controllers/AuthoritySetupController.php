<?php

namespace App\Http\Controllers;

use App\Models\Authority;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthoritySetupController extends Controller
{
    public function setup()
    {
        $categories = Category::active()->ordered()->get();
        return view('authorities.setup', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:government,ngo,academic,private',
            'jurisdiction_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'notification_email' => 'required|email',
            'monitored_categories' => 'required|array',
            'monitored_categories.*' => 'exists:categories,id',
        ]);
        
        // Create the authority
        $authority = Authority::create([
            'name' => $request->name,
            'type' => $request->type,
            'jurisdiction_name' => $request->jurisdiction_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'notification_email' => $request->notification_email,
            'notification_preferences' => ['email', 'dashboard'],
            'verification_status' => 'pending',
        ]);
        
        // Attach categories
        $authority->monitoredCategories()->attach($request->monitored_categories);
        
        // Update user role and authority
        $user = Auth::user();
        $user->role = 'authority';
        $user->authority_id = $authority->id;
        $user->save();
        
        return redirect()->route('authorities.dashboard')
            ->with('success', 'Authority profile created successfully! Your account is pending verification.');
    }
}
