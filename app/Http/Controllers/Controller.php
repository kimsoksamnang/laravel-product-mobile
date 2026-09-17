<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Get currently authenticated or default user.
     */
    protected function getActiveUser(): ?User
    {
        if ($userId = session('acting_user_id')) {
            $user = User::find($userId);
            if ($user) {
                return $user;
            }
        }
        return Auth::user();
    }

    /**
     * Get currently selected Facebook Page.
     */
    protected function getActiveFacebookPage(): ?FacebookPage
    {
        $pageId = session('active_facebook_page_id');
        if ($pageId === 'all') {
            return null;
        }
        if ($pageId) {
            $page = FacebookPage::find($pageId);
            if ($page) {
                return $page;
            }
        }

        $user = $this->getActiveUser();
        if ($user) {
            $page = $user->facebookPages()->first();
            if ($page) {
                session(['active_facebook_page_id' => $page->id]);
                return $page;
            }
        }

        $fallback = FacebookPage::first();
        if ($fallback) {
            session(['active_facebook_page_id' => $fallback->id]);
        }
        return $fallback;
    }
}
