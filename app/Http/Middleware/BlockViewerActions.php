<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockViewerActions
{
    /**
     * Handle an incoming request.
     * 
     * Blocks Viewers from accessing create, edit, update, store, and destroy routes.
     * Viewers can only view (index, show) data.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // Check if user is a Viewer
        if ($user->hasRole('viewer')) {
            // Get the route and action name
            $route = $request->route();
            
            if ($route) {
                $routeAction = $route->getActionMethod();
                
                // Block create, store, edit, update, destroy actions
                $blockedActions = ['create', 'store', 'edit', 'update', 'destroy'];
                
                if (in_array($routeAction, $blockedActions)) {
                    // Try to redirect back, but fallback to dashboard if no previous page
                    $redirectUrl = url()->previous() ?: route('admin.dashboard');
                    
                    return redirect($redirectUrl)
                        ->with('error', 'Access denied. Viewers can only view data and cannot perform create, edit, or delete operations.');
                }
            }
        }
        
        return $next($request);
    }
}
