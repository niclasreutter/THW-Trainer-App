<?php
/**
 * Test-Script für Mail-Versand mit DKIM-Validierung
 * 
 * Aufruf: php test-mail-dkim.php deine@email.de
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyRegistrationMail;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// E-Mail-Adresse aus Argument
$email = $argv[1] ?? null;

if (!$email) {
    echo "❌ Bitte eine E-Mail-Adresse angeben:\n";
    echo "   php test-mail-dkim.php test@example.com\n\n";
    exit(1);
}

echo "📧 Sende Test-Mail an: {$email}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Test-URL erstellen
$testUrl = "https://thw-trainer.de/verify-email/test/hash?expires=".time()."&signature=test";

try {
    // Mail-Konfiguration anzeigen
    echo "📋 Mail-Konfiguration:\n";
    echo "   Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "   Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "   Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "   From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")\n";
    echo "   Local Domain: " . (config('mail.mailers.smtp.local_domain') ?? 'nicht gesetzt') . "\n\n";
    
    echo "📨 Sende Mail...\n";
    
    // Mail senden
    Mail::to($email)->send(new VerifyRegistrationMail($testUrl));
    
    echo "✅ Mail erfolgreich gesendet!\n\n";
    
    echo "🔍 Nächste Schritte:\n";
    echo "   1. Prüfe ob die Mail angekommen ist\n";
    echo "   2. Schaue in den Mail-Header nach:\n";
    echo "      - DKIM-Signature vorhanden?\n";
    echo "      - Received from: sollte 'thw-trainer.de' sein, nicht [127.0.0.1]\n";
    echo "   3. Teste die Mail auf: https://www.mail-tester.com/\n\n";
    
    echo "📊 Log prüfen:\n";
    echo "   tail -f storage/logs/laravel.log | grep -i mail\n\n";
    
} catch (\Exception $e) {
    echo "❌ Fehler beim Senden:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "📋 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
