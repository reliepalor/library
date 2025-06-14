<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up Gmail SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Gmail SMTP configuration...');

        // Get Gmail credentials
        $email = $this->ask('Enter your Gmail address');
        $appPassword = $this->secret('Enter your Gmail App Password');

        // Read the current .env file
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        // Update mail configuration
        $envContent = preg_replace(
            '/MAIL_MAILER=.*/',
            'MAIL_MAILER=smtp',
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_HOST=.*/',
            'MAIL_HOST=smtp.gmail.com',
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_PORT=.*/',
            'MAIL_PORT=587',
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_USERNAME=.*/',
            'MAIL_USERNAME=' . $email,
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_PASSWORD=.*/',
            'MAIL_PASSWORD=' . $appPassword,
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_ENCRYPTION=.*/',
            'MAIL_ENCRYPTION=tls',
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_FROM_ADDRESS=.*/',
            'MAIL_FROM_ADDRESS=' . $email,
            $envContent
        );
        $envContent = preg_replace(
            '/MAIL_FROM_NAME=.*/',
            'MAIL_FROM_NAME="CSU Library"',
            $envContent
        );

        // Write back to .env file
        File::put($envPath, $envContent);

        $this->info('Email configuration has been updated successfully!');
        $this->info('Please follow these steps to get your Gmail App Password:');
        $this->info('1. Go to your Google Account settings');
        $this->info('2. Enable 2-Step Verification if not already enabled');
        $this->info('3. Go to Security > App passwords');
        $this->info('4. Select "Mail" and "Other (Custom name)"');
        $this->info('5. Enter "CSU Library" as the name');
        $this->info('6. Copy the generated 16-character password');
        $this->info('7. Use this password in the MAIL_PASSWORD field');
    }
}
