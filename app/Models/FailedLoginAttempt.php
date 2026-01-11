<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    /**
     * Check if an IP address or email is locked out
     */
    public static function isLockedOut(string $email, string $ipAddress, int $maxAttempts = 5, int $lockoutMinutes = 30): bool
    {
        $lockoutTime = now()->subMinutes($lockoutMinutes);
        
        $attempts = self::where(function ($query) use ($email, $ipAddress, $lockoutTime) {
            $query->where('email', $email)
                  ->orWhere('ip_address', $ipAddress);
        })
        ->where('attempted_at', '>=', $lockoutTime)
        ->count();

        return $attempts >= $maxAttempts;
    }

    /**
     * Record a failed login attempt
     */
    public static function record(string $email, string $ipAddress, ?string $userAgent = null): void
    {
        self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Clear failed attempts for an email or IP
     */
    public static function clear(string $email, string $ipAddress): void
    {
        self::where(function ($query) use ($email, $ipAddress) {
            $query->where('email', $email)
                  ->orWhere('ip_address', $ipAddress);
        })->delete();
    }

    /**
     * Clean up old failed attempts (older than lockout period)
     */
    public static function cleanup(int $olderThanMinutes = 60): void
    {
        self::where('attempted_at', '<', now()->subMinutes($olderThanMinutes))->delete();
    }
}
