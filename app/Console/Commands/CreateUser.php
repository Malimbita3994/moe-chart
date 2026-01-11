<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {email} {--name=} {--password=} {--status=ACTIVE}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists.");
            $this->info("To reset password, use: php artisan user:reset-password {$email} --password=yourpassword");
            return 1;
        }

        $name = $this->option('name') ?: $this->ask('Enter name', explode('@', $email)[0]);
        $fullName = $this->ask('Enter full name', $name);
        $password = $this->option('password') ?: $this->secret('Enter password (or press Enter to generate)');
        $status = $this->option('status');

        if (empty($password)) {
            $password = \Illuminate\Support\Str::random(12);
            $this->info("Generated password: {$password}");
        }

        $user = User::create([
            'name' => $name,
            'full_name' => $fullName,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => $status,
        ]);

        $this->info("\n✓ User created successfully!");
        $this->line("  Name: {$user->name}");
        $this->line("  Full Name: {$user->full_name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Status: {$user->status}");
        $this->line("  Password: {$password}");
        $this->warn("\nPlease save this password securely. It will not be shown again.");

        return 0;
    }
}
