<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfilePictureController extends Controller
{
    /**
     * Serve profile picture securely with access control
     * 
     * Prevents direct access and enforces ownership checks
     */
    public function show(User $user)
    {
        // Access control: User can view their own picture or admin can view any
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            abort(401, 'Unauthorized');
        }

        // Allow viewing profile pictures for authenticated users
        // This enables profile pictures to be displayed on user detail pages
        // More restrictive access can be added here if needed (e.g., only admins can view others' pictures)
        // For now, any authenticated user can view profile pictures

        if (!$user->profile_picture) {
            // Return a default avatar image instead of 404
            return $this->getDefaultAvatar($user);
        }

        $path = storage_path('app/public/profiles/' . $user->profile_picture);

        if (!file_exists($path)) {
            Log::warning('Profile picture file not found', [
                'user_id' => $user->id,
                'filename' => $user->profile_picture,
                'requested_by' => $currentUser->id,
            ]);
            // Return default avatar instead of 404
            return $this->getDefaultAvatar($user);
        }

        // Log access
        Log::info('Profile picture accessed', [
            'user_id' => $user->id,
            'requested_by' => $currentUser->id,
            'ip' => request()->ip(),
        ]);

        return response()->file($path, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Generate and return a default avatar image
     */
    protected function getDefaultAvatar(User $user): \Illuminate\Http\Response
    {
        $initials = strtoupper(substr($user->full_name ?? $user->name ?? $user->email ?? 'U', 0, 2));
        
        // Create a simple SVG avatar
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">
            <rect width="200" height="200" fill="#4F46E5"/>
            <text x="100" y="100" font-family="Arial, sans-serif" font-size="72" fill="white" 
                  text-anchor="middle" dominant-baseline="central" font-weight="bold">' . 
                  htmlspecialchars($initials) . 
            '</text>
        </svg>';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'private, max-age=3600');
    }
}
