<?php
require_once __DIR__.'/vendor/autoload.php';

// Create a simple script to check user roles
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$admin = User::where('email', 'admin@example.com')->first();
if($admin) {
    echo "Admin user found: " . $admin->name . "\n";
    echo "Roles: ";
    print_r($admin->roles->pluck('name')->toArray());
} else {
    echo "Admin user not found.\n";
}

$mandor = User::where('email', 'mandor@example.com')->first();
if($mandor) {
    echo "Mandor user found: " . $mandor->name . "\n";
    echo "Roles: ";
    print_r($mandor->roles->pluck('name')->toArray());
} else {
    echo "Mandor user not found.\n";
}