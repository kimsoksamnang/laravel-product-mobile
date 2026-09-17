<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacebookPageController extends Controller
{
    /**
     * Display all Facebook Pages (shops) associated with the user.
     */
    public function index(): View
    {
        $user = $this->getActiveUser();
        $pages = $user ? $user->facebookPages()->withCount(['products', 'openOrders', 'closedOrders'])->get() : FacebookPage::withCount(['products', 'openOrders', 'closedOrders'])->get();

        $activePage = $this->getActiveFacebookPage();

        return view('facebook-pages.index', compact('pages', 'activePage'));
    }

    /**
     * Show form to connect a new Facebook Page.
     */
    public function create(): View
    {
        return view('facebook-pages.create');
    }

    /**
     * Connect/Store a new Facebook Page.
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
        ]);

        $page = FacebookPage::create([
            'page_id' => $validated['page_id'],
            'name' => $validated['name'],
            'category' => $validated['category'] ?? 'E-commerce & Retail',
            'followers_count' => $validated['followers_count'] ?? 0,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'about' => $validated['about'] ?? null,
            'avatar_url' => $validated['avatar_url'] ?? null,
            'is_active' => true,
        ]);

        $user = $this->getActiveUser();
        if ($user) {
            $user->facebookPages()->attach($page->id, ['role' => 'owner']);
        }

        session(['active_facebook_page_id' => $page->id]);

        return redirect()->route('facebook-pages.index')->with('success', "Facebook Page '{$page->name}' connected successfully!");
    }

    /**
     * Switch active Facebook Page shop.
     */
    public function switchPage(Request $request, string $id): RedirectResponse
    {
        if ($id === 'all') {
            session(['active_facebook_page_id' => 'all']);
            return back()->with('success', 'Viewing all connected Facebook Pages.');
        }

        $page = FacebookPage::findOrFail($id);
        session(['active_facebook_page_id' => $page->id]);

        return back()->with('success', "Switched active shop to {$page->name}");
    }

    /**
     * View details of a specific Facebook Page.
     */
    public function show(FacebookPage $facebookPage): View
    {
        $facebookPage->load(['users', 'products' => fn($q) => $q->latest()->limit(5), 'orders' => fn($q) => $q->latest()->limit(5)]);
        return view('facebook-pages.show', ['page' => $facebookPage]);
    }

    /**
     * Toggle AI Order Agent for a specific Facebook Page.
     */
    public function toggleAiAgent(FacebookPage $facebookPage): RedirectResponse
    {
        $facebookPage->ai_order_agent_enabled = !$facebookPage->ai_order_agent_enabled;
        $facebookPage->save();

        $status = $facebookPage->ai_order_agent_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "AI Order Agent is now {$status} for '{$facebookPage->name}'.");
    }
}
