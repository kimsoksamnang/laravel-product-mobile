<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Clear any acting_user_id test session
            $request->session()->forget('acting_user_id');

            // Redirect based on role
            if (Auth::user()->isSystemAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('products.index'));
        }

        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
        ])->onlyInput('phone');
    }

    /**
     * Redirect the user to the Facebook authentication page.
     * Only requests basic scopes (email, public_profile) to avoid
     * "Invalid Scopes" errors. Page permissions are requested separately.
     */
    public function redirectToFacebook()
    {
        $driver = Socialite::driver('facebook');
        
        if ($configId = config('services.facebook.login_config_id')) {
            $driver->with(['config_id' => $configId]);
        }
        
        return $driver->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Facebook Login Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect('/login')->withErrors(['phone' => 'Failed to login with Facebook. See logs for details.']);
        }

        $user = User::where('facebook_user_id', $facebookUser->id)->first();

        if ($user) {
            // Update token if it changed
            $user->update(['facebook_access_token' => $facebookUser->token]);
        } else {
            // Check if user with this email already exists
            $user = User::where('email', $facebookUser->email)->first();

            if ($user) {
                // Link the facebook account
                $user->connectFacebook($facebookUser->id, $facebookUser->avatar, $facebookUser->token);
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email,
                    'facebook_user_id' => $facebookUser->id,
                    'avatar_url' => $facebookUser->avatar,
                    'role' => 'Shop Owner', // Default role
                    'password' => bcrypt(Str::random(16)), // Random password, they will login via FB or set it later
                    'facebook_connected_at' => now(),
                    'facebook_access_token' => $facebookUser->token,
                ]);
            }
        }

        // Sync Facebook Pages from the Graph API using the fresh token
        try {
            $response = Http::get('https://graph.facebook.com/v20.0/me/accounts', [
                'access_token' => $facebookUser->token,
                'fields'       => 'id,name,category,fan_count,picture,access_token',
            ]);

            if ($response->successful()) {
                $pages = $response->json('data', []);
                foreach ($pages as $pageData) {
                    $page = \App\Models\FacebookPage::updateOrCreate(
                        ['page_id' => $pageData['id']],
                        [
                            'name'            => $pageData['name'] ?? null,
                            'category'        => $pageData['category'] ?? null,
                            'followers_count' => $pageData['fan_count'] ?? 0,
                            'avatar_url'      => $pageData['picture']['data']['url'] ?? null,
                            'access_token'    => $pageData['access_token'] ?? null,
                            'is_active'       => true,
                        ]
                    );

                    // Link page to user in pivot table if not already linked
                    if (!$user->facebookPages()->where('facebook_pages.id', $page->id)->exists()) {
                        $user->facebookPages()->attach($page->id, ['role' => 'owner']);
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('Facebook Pages sync failed', [
                    'user_id' => $user->id,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Facebook Pages Sync Error: ' . $e->getMessage());
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('acting_user_id');

        if ($user->isSystemAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('products.index'));
    }

    /**
     * Re-authenticate with Facebook to request page permissions.
     * Called for already-logged-in users who want to connect their pages.
     *
     * NOTE: pages_show_list and pages_read_engagement must be added to your app
     * in Meta Developer Console → Use Cases → Permissions before this will work.
     */
    public function redirectToFacebookPages(Request $request)
    {
        // If Facebook returned an error (e.g. invalid scopes), catch it here
        if ($request->has('error')) {
            $errorReason  = $request->get('error_reason', '');
            $errorDesc    = $request->get('error_description', 'Facebook rejected the permission request.');

            if (str_contains($errorReason, 'scope') || str_contains($errorDesc, 'scope') || str_contains($errorDesc, 'Invalid')) {
                return redirect()->route('profile.index')->withErrors([
                    'facebook' =>
                        '⚠️ Facebook rejected the page permissions. ' .
                        'You must add "pages_show_list" and "pages_read_engagement" to your app in ' .
                        'Meta Developer Console → Use Cases → Permissions first.',
                ]);
            }

            return redirect()->route('profile.index')
                ->withErrors(['facebook' => 'Facebook error: ' . $errorDesc]);
        }

        $driver = Socialite::driver('facebook')
            ->scopes(['pages_show_list']);

        $withParams = ['auth_type' => 'rerequest'];
        if ($configId = config('services.facebook.login_config_id')) {
            $withParams['config_id'] = $configId;
        }

        return $driver->with($withParams)->redirect();
    }

    /**
     * Handle the Facebook pages permission callback.
     * Syncs the user's managed pages into the database.
     */
    public function handleFacebookPagesCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Facebook Pages Connect Error: ' . $e->getMessage());
            return redirect()->route('facebook-pages.index')
                ->withErrors(['error' => 'Failed to connect Facebook pages. Please try again.']);
        }

        $user = Auth::user();

        // Update the stored token and avatar with the new one
        $user->update([
            'facebook_access_token' => $facebookUser->token,
            'avatar_url' => $facebookUser->getAvatar() ?? $user->avatar_url,
        ]);

        // Sync Facebook Pages from the Graph API using the fresh token
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://graph.facebook.com/v20.0/me/accounts', [
                'access_token' => $facebookUser->token,
                'fields'       => 'id,name,category,fan_count,picture,access_token',
            ]);

            if ($response->successful()) {
                $pages = $response->json('data', []);

                if (empty($pages)) {
                    return redirect()->route('facebook-pages.index')
                        ->with('warning', 'No Facebook Pages found. Make sure you manage at least one Page.');
                }

                foreach ($pages as $pageData) {
                    $page = \App\Models\FacebookPage::updateOrCreate(
                        ['page_id' => $pageData['id']],
                        [
                            'name'            => $pageData['name'] ?? null,
                            'category'        => $pageData['category'] ?? null,
                            'followers_count' => $pageData['fan_count'] ?? 0,
                            'avatar_url'      => $pageData['picture']['data']['url'] ?? null,
                            'access_token'    => $pageData['access_token'] ?? null,
                            'is_active'       => true,
                        ]
                    );

                    // Link page to user in pivot table if not already linked
                    if (!$user->facebookPages()->where('facebook_pages.id', $page->id)->exists()) {
                        $user->facebookPages()->attach($page->id, ['role' => 'owner']);
                    }
                }

                return redirect()->route('facebook-pages.index')
                    ->with('success', count($pages) . ' Facebook Page(s) synced successfully!');
            }

            \Illuminate\Support\Facades\Log::warning('Facebook Pages API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return redirect()->route('facebook-pages.index')
                ->withErrors(['error' => 'Could not fetch pages from Facebook. Please try again.']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Facebook Pages Sync Error: ' . $e->getMessage());
            return redirect()->route('facebook-pages.index')
                ->withErrors(['error' => 'Page sync failed. Please try again.']);
        }
    }

    /**
     * Sync Facebook Pages using a manually-provided user access token.
     * Workaround for apps that cannot request page scopes via OAuth due to app type.
     * Get a token with pages_show_list from: https://developers.facebook.com/tools/explorer/
     */
    public function syncPagesWithToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string|min:10',
        ]);

        $token = trim($request->input('access_token'));
        $user  = Auth::user();

        try {
            $response = Http::get('https://graph.facebook.com/v20.0/me/accounts', [
                'access_token' => $token,
                'fields'       => 'id,name,category,fan_count,picture,access_token',
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message', 'Invalid token or insufficient permissions.');
                return redirect()->route('profile.index')
                    ->withErrors(['token_sync' => 'Facebook API error: ' . $errorMsg]);
            }

            $pages = $response->json('data', []);

            if (empty($pages)) {
                return redirect()->route('profile.index')
                    ->with('warning', 'Token is valid but no Facebook Pages were found. Make sure you manage at least one Page and the token has pages_show_list permission.');
            }

            // Store the token so future API calls work
            $user->update(['facebook_access_token' => $token]);

            foreach ($pages as $pageData) {
                $page = \App\Models\FacebookPage::updateOrCreate(
                    ['page_id' => $pageData['id']],
                    [
                        'name'            => $pageData['name'] ?? null,
                        'category'        => $pageData['category'] ?? null,
                        'followers_count' => $pageData['fan_count'] ?? 0,
                        'avatar_url'      => $pageData['picture']['data']['url'] ?? null,
                        'access_token'    => $pageData['access_token'] ?? null,
                        'is_active'       => true,
                    ]
                );

                if (!$user->facebookPages()->where('facebook_pages.id', $page->id)->exists()) {
                    $user->facebookPages()->attach($page->id, ['role' => 'owner']);
                }
            }

            return redirect()->route('profile.index')
                ->with('success', count($pages) . ' Facebook Page(s) synced successfully! ✅');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Manual token sync error: ' . $e->getMessage());
            return redirect()->route('profile.index')
                ->withErrors(['token_sync' => 'Sync failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)


    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
