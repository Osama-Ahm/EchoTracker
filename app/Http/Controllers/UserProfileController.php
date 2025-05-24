<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();

        // Get user statistics
        $totalReports = $user->incidents()->count();
        $resolvedReports = $user->incidents()->where('status', 'resolved')->count();
        $pendingReports = $user->incidents()->whereIn('status', ['reported', 'under_review', 'in_progress'])->count();

        // Get recent activity
        $recentReports = $user->incidents()
            ->with(['category', 'photos'])
            ->latest()
            ->take(5)
            ->get();

        // Get monthly activity for chart
        $monthlyActivity = $user->incidents()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get category breakdown
        $categoryStats = $user->incidents()
            ->with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->count];
            });

        return view('profile.show', compact(
            'user',
            'totalReports',
            'resolvedReports',
            'pendingReports',
            'recentReports',
            'monthlyActivity',
            'categoryStats'
        ));
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'notification_preferences' => 'array',
            'notification_preferences.*' => 'string|in:email,sms,push',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Delete user's incidents and photos
        foreach ($user->incidents as $incident) {
            foreach ($incident->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
            $incident->delete();
        }

        $user->delete();

        return redirect()->route('welcome')
            ->with('success', 'Account deleted successfully.');
    }
}
