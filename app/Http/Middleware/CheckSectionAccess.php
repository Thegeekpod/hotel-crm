<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $section
     */
    public function handle(Request $request, Closure $next, string $section): Response
    {
        // 1. Must be authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('warning', 'Please login to access this section.');
        }

        $user = Auth::user();

        // 2. Check if account is active
        if (strtolower($user->status ?? '') === 'inactive') {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Your account is deactivated. Please contact the administrator.');
        }

        // 3. Super admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // 4. Admin section requires admin role
        if (strtolower($section) === 'admin') {
            return redirect()->route($user->getDefaultRoute())
                ->with('error', 'Access Restricted: Administrator master control is restricted to Admin personnel only.');
        }

        // 5. Check if employee has permission for this specific section
        if ($user->hasSection($section)) {
            return $next($request);
        }

        // 6. User is not allowed this section -> redirect to their default section
        $allowed = $user->getAllowedSections();
        $allowedNames = !empty($allowed) ? implode(', ', array_map('ucwords', $allowed)) : 'None';

        return redirect()->route($user->getDefaultRoute())
            ->with('error', "Access Restricted: You do not have permission to access the " . ucfirst($section) . " section. (Your assigned section(s): {$allowedNames})");
    }
}

