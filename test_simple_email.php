<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    echo "Sending test email...\n";
    Mail::raw('Test email from Laravel', function($message) {
        $message->to('reliepalor15@gmail.com')->subject('Test Email');
    });
    echo "Email sent successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
