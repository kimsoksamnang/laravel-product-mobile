<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a paginated list of users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount('facebookPages')
            ->addSelect([
                'products_count' => \App\Models\Product::selectRaw('count(*)')
                    ->whereIn('facebook_page_id', function ($q) {
                        $q->select('facebook_page_id')
                          ->from('facebook_page_user')
                          ->whereColumn('user_id', 'users.id');
                    }),
                'orders_count' => \App\Models\Order::selectRaw('count(*)')
                    ->whereIn('facebook_page_id', function ($q) {
                        $q->select('facebook_page_id')
                          ->from('facebook_page_user')
                          ->whereColumn('user_id', 'users.id');
                    })
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('facebookPages', function ($q2) use ($search) {
                      $q2->where('page_id', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('page_id')) {
            $query->whereHas('facebookPages', function ($q) use ($request) {
                $q->where('facebook_pages.id', $request->page_id);
            });
        }

        if ($request->filled('role_filter')) {
            if ($request->role_filter === 'admin') {
                $query->where('is_system_admin', true);
            } elseif ($request->role_filter === 'merchant') {
                $query->where('is_system_admin', false);
            }
        }

        if ($request->filled('fb_filter')) {
            if ($request->fb_filter === 'connected') {
                $query->whereNotNull('facebook_connected_at');
            } elseif ($request->fb_filter === 'disconnected') {
                $query->whereNull('facebook_connected_at');
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $currentUser = $this->getActiveUser();

        return view('admin.users.index', compact('users', 'currentUser'));
    }

    /**
     * Show form to create a new user.
     */
    public function create(): View
    {
        $allPages = FacebookPage::orderBy('name')->get();
        return view('admin.users.create', compact('allPages'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'is_system_admin' => 'nullable|boolean',
            'pages' => 'nullable|array',
            'pages.*' => 'exists:facebook_pages,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'Merchant',
            'bio' => $validated['bio'] ?? null,
            'is_system_admin' => $request->boolean('is_system_admin'),
        ]);

        if (!empty($validated['pages'])) {
            $user->facebookPages()->sync($validated['pages']);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} created successfully!");
    }

    /**
     * Show form to edit user details and assigned Facebook Pages.
     */
    public function edit(User $user): View
    {
        $user->load('facebookPages');
        $allPages = FacebookPage::orderBy('name')->get();
        $currentUser = $this->getActiveUser();

        return view('admin.users.edit', compact('user', 'allPages', 'currentUser'));
    }

    /**
     * Update user details and assigned Facebook Pages.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'is_system_admin' => 'nullable|boolean',
            'pages' => 'nullable|array',
            'pages.*' => 'exists:facebook_pages,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'Merchant',
            'bio' => $validated['bio'] ?? null,
            'is_system_admin' => $request->boolean('is_system_admin'),
        ]);

        $user->facebookPages()->sync($request->input('pages', []));

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} updated successfully!");
    }

    /**
     * Toggle Facebook connection status for a user.
     */
    public function toggleFacebook(User $user): RedirectResponse
    {
        if ($user->isFacebookConnected()) {
            $user->disconnectFacebook();
            $msg = "Disconnected Facebook from user {$user->name}.";
        } else {
            $fbId = 'fb_' . rand(10000000, 99999999);
            $user->connectFacebook($fbId);
            $msg = "Connected Facebook ({$fbId}) for user {$user->name}.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Remove a user from the system.
     */
    public function destroy(User $user): RedirectResponse
    {
        $currentUser = $this->getActiveUser();
        if ($currentUser && $currentUser->id === $user->id) {
            return back()->with('error', 'Cannot delete the currently active user.');
        }

        $user->facebookPages()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} deleted successfully.");
    }
}
