<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:50'],
            'employee_number' => ['nullable', 'string', 'max:100', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Get Viewer role as default
        $viewerRole = Role::where('slug', 'viewer')->first();
        
        $user = User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'ACTIVE',
        ]);
        
        // Explicitly set role_id (prevent mass assignment)
        if ($viewerRole) {
            $user->role_id = $viewerRole->id;
            $user->save();
        }

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Don't auto-login - require email verification first
        return redirect()->route('login')
            ->with('status', 'Registration successful! Please check your email to verify your account before logging in.');
    }
}
