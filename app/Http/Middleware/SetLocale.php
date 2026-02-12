<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale from session or defaults to 'en'
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, default to 'en'
        $locale = $request->session()->get('locale', 'en');
        
        // Validate locale (only allow 'en' or 'sw')
        $allowedLocales = ['en', 'sw'];
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'en';
        }
        
        // Set application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}
