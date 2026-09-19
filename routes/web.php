<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFacebookPageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/login/facebook', [LoginController::class, 'redirectToFacebook'])->name('login.facebook')->middleware('guest');
Route::get('/login/facebook/callback', [LoginController::class, 'handleFacebookCallback'])->middleware('guest');

// Re-auth to request page permissions (for already-logged-in users)
Route::get('/connect/facebook/pages', [LoginController::class, 'redirectToFacebookPages'])->name('facebook.connect-pages')->middleware('auth');
Route::get('/connect/facebook/pages/callback', [LoginController::class, 'handleFacebookPagesCallback'])->middleware('auth');

// Manual token workaround: sync pages using a token from Graph API Explorer
Route::post('/connect/facebook/pages/manual', [LoginController::class, 'syncPagesWithToken'])->name('facebook.sync-pages-manual')->middleware('auth');

Route::get('/', function () {
    return redirect()->route('products.index');
});

// ⚠️ TEMPORARY DEBUG ROUTE — Remove before going live
Route::get('/debug/me', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not logged in'], 401);
    }

    // Call Graph API to verify the token is valid and get identity
    $token = $user->getRawOriginal('facebook_access_token');
    $graphData = null;

    if ($token) {
        $response = \Illuminate\Support\Facades\Http::get('https://graph.facebook.com/v20.0/me', [
            'access_token' => $token,
            'fields'       => 'id,name,email',
        ]);
        $graphData = $response->json();
    }

    return response()->json([
        'db_user' => [
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'facebook_user_id'     => $user->facebook_user_id,
            'facebook_connected'   => $user->isFacebookConnected(),
            'has_access_token'     => !empty($token),
        ],
        'graph_api_identity'       => $graphData,
        'instructions'             => [
            'step1' => 'Check that graph_api_identity.id matches db_user.facebook_user_id',
            'step2' => 'Visit https://developers.facebook.com/apps → Your App → App Roles → confirm your FB ID is listed as Admin or Developer',
            'step3' => 'Your FB ID is shown in graph_api_identity.id',
        ],
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware('auth')->name('debug.me');

// Main App Routes (Protected by auth)
Route::middleware(['auth'])->group(function () {
    // 1. User Profile Menu & Facebook Connection
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/connect-facebook', [UserProfileController::class, 'connectFacebook'])->name('profile.connect-facebook');
    Route::post('/profile/disconnect-facebook', [UserProfileController::class, 'disconnectFacebook'])->name('profile.disconnect-facebook');

    // 2. Facebook Page Menu & Shop Switcher
    Route::get('/facebook-pages', [FacebookPageController::class, 'index'])->name('facebook-pages.index');
    Route::get('/facebook-pages/create', [FacebookPageController::class, 'create'])->name('facebook-pages.create');
    Route::post('/facebook-pages', [FacebookPageController::class, 'store'])->name('facebook-pages.store');
    Route::post('/facebook-pages/switch/{id}', [FacebookPageController::class, 'switchPage'])->name('facebook-pages.switch');
    Route::post('/facebook-pages/{facebookPage}/toggle-ai', [FacebookPageController::class, 'toggleAiAgent'])->name('facebook-pages.toggle-ai');
    Route::get('/facebook-pages/{facebookPage}', [FacebookPageController::class, 'show'])->name('facebook-pages.show');

    // 3. Products Menu
    Route::get('/products/export', [ProductController::class, 'exportCsv'])->name('products.export');
    Route::post('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    Route::resource('products', ProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // 4. Open Orders Menu
    Route::get('/orders', function () {
        return redirect()->route('orders.open');
    })->name('orders.index');
    Route::get('/orders/open', [OrderController::class, 'open'])->name('orders.open');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // 5. Closed Orders Menu (Success & Fail)
    Route::get('/orders/closed', [OrderController::class, 'closed'])->name('orders.closed');

    // Order Details, Chat/Comment History & Status Workflow
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/messages', [OrderController::class, 'addMessage'])->name('orders.add-message');

    // Metrics / Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 6. System Admin Management Console
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/switch-user/{user}', [AdminDashboardController::class, 'switchUser'])->name('switch-user');

        // Manage Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-facebook', [AdminUserController::class, 'toggleFacebook'])->name('users.toggle-facebook');
        Route::post('/users/{user}/sync-facebook-pages', [AdminUserController::class, 'syncFacebookPages'])->name('users.sync-facebook-pages');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Manage Facebook Pages
        Route::get('/facebook-pages', [AdminFacebookPageController::class, 'index'])->name('facebook-pages.index');
        Route::get('/facebook-pages/create', [AdminFacebookPageController::class, 'create'])->name('facebook-pages.create');
        Route::post('/facebook-pages', [AdminFacebookPageController::class, 'store'])->name('facebook-pages.store');
        Route::get('/facebook-pages/{facebookPage}/edit', [AdminFacebookPageController::class, 'edit'])->name('facebook-pages.edit');
        Route::put('/facebook-pages/{facebookPage}', [AdminFacebookPageController::class, 'update'])->name('facebook-pages.update');
        Route::post('/facebook-pages/{facebookPage}/toggle-data', [AdminFacebookPageController::class, 'toggleData'])->name('facebook-pages.toggle-data');
        Route::post('/facebook-pages/{facebookPage}/toggle-ai', [AdminFacebookPageController::class, 'toggleAiAgent'])->name('facebook-pages.toggle-ai');
        Route::delete('/facebook-pages/{facebookPage}', [AdminFacebookPageController::class, 'destroy'])->name('facebook-pages.destroy');
    });
});
