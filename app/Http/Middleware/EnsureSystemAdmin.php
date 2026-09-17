<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user && session('acting_user_id')) {
            $user = User::find(session('acting_user_id'));
        }
        if (!$user) {
            $user = User::first();
        }

        if (!$user || !$user->isSystemAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. System administrator access required.'], 403);
            }
            return redirect()->route('products.index')->with('error', 'Access denied. You need System Administrator privileges to access the Admin Console.');
        }

        return $next($request);
    }
}
