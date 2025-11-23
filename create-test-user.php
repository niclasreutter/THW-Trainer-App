<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@test.de',
    'password' => bcrypt('test'),
    'email_verified_at' => now(),
]);

echo "✅ User erfolgreich erstellt:\n";
echo "📧 Email: " . $user->email . "\n";
echo "🔑 Password: test\n";
echo "🆔 ID: " . $user->id . "\n";
