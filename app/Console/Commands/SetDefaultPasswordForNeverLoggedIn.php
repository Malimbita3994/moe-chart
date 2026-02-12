<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetDefaultPasswordForNeverLoggedIn extends Command
{
    protected $signature = 'users:set-default-password-for-never-logged-in
                            {--dry-run : Show count and list only, do not update passwords}';
    protected $description = 'Set the default staff password for all users who have never logged in (last_login_at is null)';

    public function handle(): int
    {
        $password = config('auth.default_staff_password', 'Password@2026');

        $query = User::whereNull('last_login_at')->orderBy('id');
        $users = $query->get();
        $count = $users->count();

        if ($count === 0) {
            $this->info('No users found who have never logged in (all have last_login_at set).');
            return 0;
        }

        $this->info("Default password (from config): " . $password);
        $this->info("Users who have never logged in: {$count}");

        if ($this->option('dry-run')) {
            $this->warn('Dry run – no changes made. Run without --dry-run to set passwords.');
            $this->table(
                ['ID', 'Name', 'Email'],
                $users->map(fn ($u) => [$u->id, $u->full_name ?? $u->name, $u->email])->toArray()
            );
            return 0;
        }

        $hash = Hash::make($password);
        foreach ($users as $user) {
            $user->password = $hash;
            $user->saveQuietly();
        }

        $this->info("Set default password for {$count} user(s). They can log in with the configured default password and should change it after first login.");
        return 0;
    }
}
