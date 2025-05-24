<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Redirect admin users to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Get statistics
        $totalIncidents = Incident::count();
        $myIncidents = Incident::where('user_id', $user->id)->count();
        $recentIncidents = Incident::with(['category', 'user'])
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

        return view('dashboard.index', compact(
            'totalIncidents',
            'myIncidents',
            'recentIncidents',
            'statusStats',
            'categoryStats'
        ));
    }
}
