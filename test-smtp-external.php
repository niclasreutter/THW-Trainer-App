#!/usr/bin/env php
<?php
/**
 * SMTP Test an externe Adresse
 * Usage: php test-smtp-external.php deine@gmail.com
 */

if (empty($argv[1])) {
    echo "Usage: php test-smtp-external.php EMPFAENGER@EMAIL.DE\n";
    echo "Beispiel: php test-smtp-external.php dein.name@gmail.com\n";
    exit(1);
}

$to = $argv[1];

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['MAIL_HOST'] ?? 'web10.bero-host.de';
$port = (int) ($_ENV['MAIL_PORT'] ?? 587);
$user = $_ENV['MAIL_USERNAME'] ?? '';
$pass = $_ENV['MAIL_PASSWORD'] ?? '';
$from = $_ENV['MAIL_FROM_ADDRESS'] ?? '';

echo "Sende Test-Mail an: $to\n";
echo "Via: $host:$port als $user\n\n";

$socket = @fsockopen($host, $port, $errno, $errstr, 10);
if (!$socket) { echo "FEHLER: Verbindung fehlgeschlagen\n"; exit(1); }

$response = fgets($socket, 1024);
echo "S: $response";

fwrite($socket, "EHLO thw-trainer.de\r\n");
while ($line = fgets($socket, 1024)) { echo "S: $line"; if (preg_match('/^\d{3} /', $line)) break; }

fwrite($socket, "STARTTLS\r\n");
$line = fgets($socket, 1024); echo "S: $line";
stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);

fwrite($socket, "EHLO thw-trainer.de\r\n");
while ($line = fgets($socket, 1024)) { if (preg_match('/^\d{3} /', $line)) break; }

fwrite($socket, "AUTH LOGIN\r\n");
fgets($socket, 1024);
fwrite($socket, base64_encode($user) . "\r\n");
fgets($socket, 1024);
fwrite($socket, base64_encode($pass) . "\r\n");
$line = fgets($socket, 1024);
echo "Auth: $line";

fwrite($socket, "MAIL FROM:<$from>\r\n");
$line = fgets($socket, 1024); echo "MAIL FROM: $line";

fwrite($socket, "RCPT TO:<$to>\r\n");
$line = fgets($socket, 1024); echo "RCPT TO: $line";

fwrite($socket, "DATA\r\n");
$line = fgets($socket, 1024); echo "DATA: $line";

$dateStr = date('r');
$msgId = uniqid('test-') . '@thw-trainer.de';
$body = "From: THW-Trainer <$from>\r\n"
    . "To: <$to>\r\n"
    . "Subject: =?UTF-8?B?" . base64_encode("Mail-Test " . date('H:i:s')) . "?=\r\n"
    . "Date: $dateStr\r\n"
    . "Message-ID: <$msgId>\r\n"
    . "MIME-Version: 1.0\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n"
    . "\r\n"
    . "Dies ist ein Test ob Mails von thw-trainer.de ankommen.\r\n"
    . "Gesendet: " . date('Y-m-d H:i:s') . "\r\n"
    . "Queue-ID wird unten angezeigt.\r\n"
    . ".\r\n";

fwrite($socket, $body);
$line = fgets($socket, 1024);
echo "RESULT: $line";

fwrite($socket, "QUIT\r\n");
fgets($socket, 1024);
fclose($socket);

echo "\n--- Fertig ---\n";
echo "Pruefe jetzt:\n";
echo "1. Postfach von $to (Inbox + Spam)\n";
echo "2. Wenn auch nach 5 Min nichts kommt → Problem bei BERO HOST\n";
