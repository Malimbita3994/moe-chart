<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password {email} {--password=} {--show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for a user by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $this->info("User found:");
        $this->line("  Name: {$user->name}");
        $this->line("  Full Name: {$user->full_name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Status: {$user->status}");
        $this->line("  Employee Number: " . ($user->employee_number ?? 'N/A'));

        if ($this->option('show')) {
            // We can't show the actual password, but we can show account details
            $this->warn("\nNote: Passwords are hashed and cannot be retrieved.");
            $this->info("To reset the password, use: php artisan user:reset-password {$email} --password=yournewpassword");
            return 0;
        }

        $password = $this->option('password');
        
        if (!$password) {
            $password = $this->secret('Enter new password (or press Enter to generate a random one)');
            
            if (empty($password)) {
                $password = \Illuminate\Support\Str::random(12);
                $this->info("Generated password: {$password}");
            }
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("\n✓ Password reset successfully!");
        $this->line("  Email: {$user->email}");
        $this->line("  New Password: {$password}");
        $this->warn("\nPlease save this password securely. It will not be shown again.");

        return 0;
    }
}
