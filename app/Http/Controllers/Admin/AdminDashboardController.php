<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display System Admin Dashboard overview with platform stats.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'connected_users' => User::whereNotNull('facebook_connected_at')->count(),
            'total_pages' => FacebookPage::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Allow System Admin to impersonate or switch user context rapidly.
     * This mimics the 'Switch Facebook Page' logic but at the User level.
     */
    public function switchUser(Request $request, User $user): RedirectResponse
    {
        // Must be a system admin to switch users
        if (!Auth::user()->isSystemAdmin()) {
            abort(403, 'Unauthorized.');
        }

        // We do NOT log them out. We simply store the intended "acting_user_id" in session.
        // In a real app, you would have a middleware that overrides Auth::user() based on this session.
        session(['acting_user_id' => $user->id]);

        return redirect()->back()->with('success', "Switched context to {$user->name}.");
    }
}
