<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateArgusAdmin extends Command
{
    protected $signature = 'argus:admin-create';

    protected $description = 'Create or update the ARCLUS  administrator account';

    public function handle(): int
    {
        $name = $this->ask('Administrator name');
        $email = strtolower(trim($this->ask('Administrator email')));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.');
            return self::FAILURE;
        }

        $password = $this->secret('Administrator password');
        $confirmation = $this->secret('Confirm password');

        if (empty($password) || $password !== $confirmation) {
            $this->error('Passwords are empty or do not match.');
            return self::FAILURE;
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'is_active' => true,
            ]
        );

        $this->info('ARCLUS administrator account saved successfully.');

        return self::SUCCESS;
    }
}
