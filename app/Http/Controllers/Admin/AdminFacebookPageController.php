<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFacebookPageController extends Controller
{
    /**
     * Display a paginated list of all Facebook Pages with AI agent status.
     */
    public function index(Request $request): View
    {
        $query = FacebookPage::withCount(['users', 'products', 'orders']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('page_id', 'like', "%{$search}%")
                  ->orWhereHas('users', function ($q2) use ($search) {
                      $q2->where('users.id', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('user_id')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }

        if ($request->filled('ai_filter')) {
            if ($request->ai_filter === 'enabled') {
                $query->where('ai_order_agent_enabled', true);
            } elseif ($request->ai_filter === 'disabled') {
                $query->where('ai_order_agent_enabled', false);
            }
        }

        $pages = $query->latest()->paginate(10)->withQueryString();
        $currentUser = $this->getActiveUser();

        return view('admin.pages.index', compact('pages', 'currentUser'));
    }

    /**
     * Show form to register/connect a new Facebook Page.
     */
    public function create(): View
    {
        $allUsers = User::orderBy('name')->get();
        return view('admin.pages.create', compact('allUsers'));
    }

    /**
     * Store a new Facebook Page with AI agent setting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'page_id' => 'required|string|max:100|unique:facebook_pages,page_id',
            'category' => 'nullable|string|max:255',
            'followers_count' => 'nullable|integer|min:0',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'about' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:500',
            'data_enabled' => 'nullable|boolean',
            'ai_order_agent_enabled' => 'nullable|boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $dataEnabled = $request->boolean('data_enabled');

        $page = FacebookPage::create([
            'name' => $validated['name'],
            'page_id' => $validated['page_id'],
            'category' => $validated['category'] ?? 'E-commerce & Retail',
            'followers_count' => $validated['followers_count'] ?? 0,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'about' => $validated['about'] ?? null,
            'avatar_url' => $validated['avatar_url'] ?? null,
            'is_active' => true,
            'data_enabled' => $dataEnabled,
            'ai_order_agent_enabled' => $dataEnabled ? $request->boolean('ai_order_agent_enabled') : false,
        ]);

        if (!empty($validated['users'])) {
            $page->users()->sync($validated['users']);
        }

        return redirect()->route('admin.facebook-pages.index')->with('success', "Facebook Page '{$page->name}' created successfully!");
    }

    /**
     * Show form to edit Facebook Page and toggle AI Order Agent.
     */
    public function edit(FacebookPage $facebookPage): View
    {
        $facebookPage->load('users');
        $allUsers = User::orderBy('name')->get();
        $currentUser = $this->getActiveUser();

        return view('admin.pages.edit', ['page' => $facebookPage, 'allUsers' => $allUsers, 'currentUser' => $currentUser]);
    }

    /**
     * Update Facebook Page settings and member assignments.
     */
    public function update(Request $request, FacebookPage $facebookPage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'followers_count' => 'nullable|integer|min:0',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'about' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:500',
            'data_enabled' => 'nullable|boolean',
            'ai_order_agent_enabled' => 'nullable|boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $dataEnabled = $request->boolean('data_enabled');

        $facebookPage->update([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? $facebookPage->category,
            'followers_count' => $validated['followers_count'] ?? $facebookPage->followers_count,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'about' => $validated['about'] ?? null,
            'avatar_url' => $validated['avatar_url'] ?? $facebookPage->avatar_url,
            'data_enabled' => $dataEnabled,
            'ai_order_agent_enabled' => $dataEnabled ? $request->boolean('ai_order_agent_enabled') : false,
        ]);

        $facebookPage->users()->sync($request->input('users', []));

        return redirect()->route('admin.facebook-pages.index')->with('success', "Facebook Page '{$facebookPage->name}' updated successfully!");
    }

    /**
     * Quick toggle for Data Enablement on a Facebook Page.
     */
    public function toggleData(FacebookPage $facebookPage): RedirectResponse
    {
        $facebookPage->data_enabled = !$facebookPage->data_enabled;
        
        // If data is turned off, AI must be turned off too.
        if (!$facebookPage->data_enabled) {
            $facebookPage->ai_order_agent_enabled = false;
        }
        
        $facebookPage->save();

        $status = $facebookPage->data_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "Data Sync is now {$status} for page '{$facebookPage->name}'.");
    }

    /**
     * Quick toggle for AI Order Agent on a Facebook Page.
     */
    public function toggleAiAgent(FacebookPage $facebookPage): RedirectResponse
    {
        if (!$facebookPage->isDataEnabled()) {
            return back()->with('error', "Cannot enable AI Agent for '{$facebookPage->name}' because Data is not ready/enabled.");
        }

        $facebookPage->ai_order_agent_enabled = !$facebookPage->ai_order_agent_enabled;
        $facebookPage->save();

        $status = $facebookPage->ai_order_agent_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "AI Order Agent is now {$status} for page '{$facebookPage->name}'.");
    }

    /**
     * Delete a Facebook Page.
     */
    public function destroy(FacebookPage $facebookPage): RedirectResponse
    {
        $facebookPage->users()->detach();
        $facebookPage->delete();

        return redirect()->route('admin.facebook-pages.index')->with('success', "Facebook Page '{$facebookPage->name}' removed successfully.");
    }
}
