<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserVerificationStatus extends Command
{
    protected $signature = 'users:fix-status';
    protected $description = 'Fix users verification status based on email_verified_at';

    public function handle()
    {
        $this->info('Starting user verification status cleanup...');

        // Set verified if email_verified_at exists
        $verifiedCount = User::whereNotNull('email_verified_at')
            ->update(['status' => 'verified']);

        $this->info("Set $verifiedCount users as VERIFIED");

        // Set not verified if email_verified_at is null
        $notVerifiedCount = User::whereNull('email_verified_at')
            ->update(['status' => 'not verified']);

        $this->info("Set $notVerifiedCount users as NOT VERIFIED");

        $this->info('User verification status cleanup completed!');
    }
}
