<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FailedLoginAttempt;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Account lockout configuration
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 30;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $credentials['email'];
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if account is locked out
        if (FailedLoginAttempt::isLockedOut($email, $ipAddress, self::MAX_ATTEMPTS, self::LOCKOUT_MINUTES)) {
            // Log lockout attempt
            FailedLoginAttempt::record($email, $ipAddress, $userAgent);
            
            throw ValidationException::withMessages([
                'email' => __('Your account has been temporarily locked due to too many failed login attempts. Please try again in ' . self::LOCKOUT_MINUTES . ' minutes.'),
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Clear failed attempts on successful login
            FailedLoginAttempt::clear($email, $ipAddress);
            
            $request->session()->regenerate();
            
            // Log login
            AuditService::logLogin(Auth::user());
            
            return redirect()->intended(route('admin.dashboard'));
        }

        // Record failed login attempt
        FailedLoginAttempt::record($email, $ipAddress, $userAgent);
        
        // Clean up old attempts (older than lockout period)
        FailedLoginAttempt::cleanup(self::LOCKOUT_MINUTES * 2);

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout before session is invalidated
        if ($user) {
            AuditService::logLogout($user);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
