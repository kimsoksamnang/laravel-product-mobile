<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display the Facebook user profile.
     */
    public function index(): View
    {
        $user = $this->getActiveUser();
        $pages = $user ? $user->facebookPages : collect();

        $stats = [
            'total_pages' => $pages->count(),
            'total_products' => $pages->sum(fn($p) => $p->products()->count()),
            'total_orders' => $pages->sum(fn($p) => $p->orders()->count()),
            'open_orders' => $pages->sum(fn($p) => $p->openOrders()->count()),
        ];

        return view('profile.index', compact('user', 'pages', 'stats'));
    }

    /**
     * Update user profile settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->getActiveUser();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'role' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:500',
        ]);

        if ($user) {
            $user->update($validated);
        }

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }

    /**
     * Connect Facebook account for user.
     */
    public function connectFacebook(Request $request): RedirectResponse
    {
        $user = $this->getActiveUser();
        if (!$user) {
            return redirect()->route('profile.index')->with('error', 'No active user found.');
        }

        $validated = $request->validate([
            'facebook_user_id' => 'nullable|string|max:100',
            'fb_profile_url' => 'nullable|url|max:255',
        ]);

        $fbId = $validated['facebook_user_id'] ?: ('fb_' . rand(10000000, 99999999));
        $fbUrl = $validated['fb_profile_url'] ?: ('https://facebook.com/' . $fbId);

        $user->connectFacebook($fbId, $fbUrl);

        return redirect()->route('profile.index')->with('success', "Facebook account ($fbId) connected successfully!");
    }

    /**
     * Disconnect Facebook account for user.
     */
    public function disconnectFacebook(): RedirectResponse
    {
        $user = $this->getActiveUser();
        if ($user) {
            $user->disconnectFacebook();
        }

        return redirect()->route('profile.index')->with('success', 'Facebook account disconnected successfully.');
    }
}
