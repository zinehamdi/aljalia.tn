<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendWelcomeEmailsToOldUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-welcome-emails-to-old-users';
    
    protected $description = 'Send welcome emails to existing users who registered before the feature was added';

    public function handle()
    {
        $users = \App\Models\User::all();
        
        $this->info("Found " . $users->count() . " users.");
        
        $count = 0;
        foreach ($users as $user) {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user));
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully sent welcome emails to $count users!");
    }
}
