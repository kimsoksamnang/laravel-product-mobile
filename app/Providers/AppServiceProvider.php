<?php

namespace App\Providers;

use App\Models\FacebookPage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $currentUser = session('acting_user_id') ? User::find(session('acting_user_id')) : Auth::user();
            $userPages = $currentUser ? $currentUser->facebookPages : collect();

            $activePageId = session('active_facebook_page_id');
            $activePage = null;

            if ($activePageId && $activePageId !== 'all') {
                $activePage = $userPages->firstWhere('id', $activePageId) ?? FacebookPage::find($activePageId);
            }

            if (!$activePage && $activePageId !== 'all') {
                $activePage = $userPages->first();
                if ($activePage) {
                    session(['active_facebook_page_id' => $activePage->id]);
                }
            }

            $openOrdersCount = Order::open()
                ->when($activePage && $activePageId !== 'all', fn($q) => $q->where('facebook_page_id', $activePage->id))
                ->count();

            $view->with([
                'currentUser' => $currentUser,
                'userPages' => $userPages,
                'activePage' => $activePage,
                'isAllPages' => $activePageId === 'all',
                'openOrdersCount' => $openOrdersCount,
            ]);
        });
    }
}
