#!/usr/bin/env php
<?php
/**
 * SMTP Debug Test - zeigt die komplette Server-Antwort
 * Usage: php test-smtp-debug.php
 */

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['MAIL_HOST'] ?? 'web10.bero-host.de';
$port = (int) ($_ENV['MAIL_PORT'] ?? 587);
$user = $_ENV['MAIL_USERNAME'] ?? '';
$pass = $_ENV['MAIL_PASSWORD'] ?? '';
$from = $_ENV['MAIL_FROM_ADDRESS'] ?? '';

echo "=== SMTP Debug Test ===\n";
echo "Host: $host:$port\n";
echo "User: $user\n";
echo "From: $from\n";
echo "To:   $from (sending to self)\n\n";

// Symfony Mailer (used by Laravel) with debug
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

try {
    $transport = new EsmtpTransport($host, $port);
    $transport->setUsername($user);
    $transport->setPassword($pass);

    // Enable debug logging
    $logger = new class implements \Psr\Log\LoggerInterface {
        use \Psr\Log\LoggerTrait;
        public function log($level, $message, array $context = []): void {
            echo "[$level] $message\n";
        }
    };

    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from($from)
        ->to($from) // An sich selbst senden
        ->subject('SMTP Debug Test ' . date('H:i:s'))
        ->text('Dies ist ein SMTP Debug Test von ' . date('Y-m-d H:i:s'));

    echo "--- Sending... ---\n\n";

    // Raw socket test first
    echo "=== Raw SMTP Conversation ===\n";
    $socket = @fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) {
        echo "ERROR: Cannot connect to $host:$port - $errstr ($errno)\n";
        exit(1);
    }

    $response = fgets($socket, 1024);
    echo "S: $response";

    $commands = [
        "EHLO thw-trainer.de\r\n",
        "STARTTLS\r\n",
    ];

    foreach ($commands as $cmd) {
        fwrite($socket, $cmd);
        echo "C: " . trim($cmd) . "\n";
        while ($line = fgets($socket, 1024)) {
            echo "S: $line";
            // If line starts with "250 " (not "250-"), it's the last line
            if (preg_match('/^\d{3} /', $line)) break;
        }
    }

    // Upgrade to TLS
    $tlsResult = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
    if (!$tlsResult) {
        echo "ERROR: TLS upgrade failed\n";
        fclose($socket);
        exit(1);
    }
    echo "--- TLS established ---\n";

    // EHLO again after TLS
    $commands2 = [
        "EHLO thw-trainer.de\r\n",
        "AUTH LOGIN\r\n",
        base64_encode($user) . "\r\n",
        base64_encode($pass) . "\r\n",
        "MAIL FROM:<$from>\r\n",
        "RCPT TO:<$from>\r\n",
        "DATA\r\n",
    ];

    foreach ($commands2 as $i => $cmd) {
        fwrite($socket, $cmd);
        // Don't echo passwords
        if ($i == 2 || $i == 3) {
            echo "C: [credentials]\n";
        } else {
            echo "C: " . trim($cmd) . "\n";
        }
        while ($line = fgets($socket, 1024)) {
            echo "S: $line";
            if (preg_match('/^\d{3} /', $line)) break;
        }
    }

    // Send email content
    $dateStr = date('r');
    $msgId = uniqid() . '@thw-trainer.de';
    $body = "From: THW-Trainer <$from>\r\n"
        . "To: <$from>\r\n"
        . "Subject: SMTP Debug Test " . date('H:i:s') . "\r\n"
        . "Date: $dateStr\r\n"
        . "Message-ID: <$msgId>\r\n"
        . "MIME-Version: 1.0\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "\r\n"
        . "Dies ist ein SMTP Debug Test.\r\n"
        . ".\r\n";

    fwrite($socket, $body);
    echo "C: [message body + .]\n";
    while ($line = fgets($socket, 1024)) {
        echo "S: $line";
        if (preg_match('/^\d{3} /', $line)) break;
    }

    fwrite($socket, "QUIT\r\n");
    echo "C: QUIT\n";
    $line = fgets($socket, 1024);
    if ($line) echo "S: $line";

    fclose($socket);

    echo "\n=== Done ===\n";
    echo "If you see '250 2.0.0 Ok: queued as XXXXX' above, the mail was accepted by BERO HOST.\n";
    echo "If the mail still doesn't arrive, the problem is on BERO HOST's side (delivery to recipient).\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
