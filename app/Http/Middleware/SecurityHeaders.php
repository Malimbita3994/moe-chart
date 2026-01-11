<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * Adds security headers to all responses to protect against common attacks.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy
        // Allows necessary CDNs while maintaining security
        // Note: Tailwind CSS is now compiled via Vite, so cdn.tailwindcss.com is no longer needed
        
        // Check if we're in development mode (Vite dev server)
        $isDevelopment = app()->environment('local', 'development');
        
        // Vite dev server URLs (for development)
        // Include both localhost and 127.0.0.1 to cover IPv4
        // Allow common Vite ports (5173, 5174, etc.) for flexibility
        // WebSocket connections only needed in connect-src
        if ($isDevelopment) {
            // Common Vite dev server ports
            $vitePorts = ['5173', '5174', '5175', '5176'];
            $viteHosts = [];
            $viteWs = [];
            
            foreach ($vitePorts as $port) {
                $viteHosts[] = "http://localhost:{$port}";
                $viteHosts[] = "http://127.0.0.1:{$port}";
                $viteWs[] = "ws://localhost:{$port}";
                $viteWs[] = "ws://127.0.0.1:{$port}";
                $viteWs[] = "wss://localhost:{$port}";
                $viteWs[] = "wss://127.0.0.1:{$port}";
            }
            
            $viteHostsStr = implode(' ', $viteHosts);
            $viteWsStr = implode(' ', $viteWs);
            
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   $viteHostsStr . " " .
                   "https://cdn.jsdelivr.net " .
                   "https://code.jquery.com; " .
                   "style-src 'self' 'unsafe-inline' " .
                   $viteHostsStr . " " .
                   "https://cdn.jsdelivr.net " .
                   "https://fonts.bunny.net; " .
                   "img-src 'self' data: https:; " .
                   "font-src 'self' data: " .
                   "https://fonts.bunny.net; " .
                   "connect-src 'self' " .
                   $viteHostsStr . " " .
                   $viteWsStr . " " .
                   "https://cdn.jsdelivr.net; " .
                   "frame-src 'self';";
        } else {
            // Production CSP (no Vite dev server)
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "https://cdn.jsdelivr.net " .
                   "https://code.jquery.com; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "https://cdn.jsdelivr.net " .
                   "https://fonts.bunny.net; " .
                   "img-src 'self' data: https:; " .
                   "font-src 'self' data: " .
                   "https://fonts.bunny.net; " .
                   "connect-src 'self' " .
                   "https://cdn.jsdelivr.net; " .
                   "frame-src 'self';";
        }
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
