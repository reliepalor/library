<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

try {
    Mail::to('reliepalor15@gmail.com')->send(new TestEmail());
    echo "Test email sent successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
