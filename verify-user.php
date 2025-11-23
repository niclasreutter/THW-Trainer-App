<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update direkt via DB
DB::table('users')
    ->where('email', 'test@test.de')
    ->update(['email_verified_at' => now()]);

$user = \App\Models\User::where('email', 'test@test.de')->first();
if ($user && $user->email_verified_at) {
    echo "✅ User verifiziert!\n";
    echo "📧 Email: " . $user->email . "\n";
    echo "✓ Verifiziert am: " . $user->email_verified_at . "\n";
} else {
    echo "❌ Fehler beim Verifizieren\n";
}
