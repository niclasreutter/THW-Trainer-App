<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account-Löschung Warnung</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #fbbf24;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .warning-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .warning-title {
            color: #dc2626;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .warning-subtitle {
            color: #f59e0b;
            font-size: 18px;
            font-weight: 600;
        }
        .content {
            margin-bottom: 30px;
        }
        .highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .account-info {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏆 THW-Trainer.de</div>
            <div class="warning-icon">⚠️</div>
            <div class="warning-title">Account-Löschung in 2 Tagen!</div>
            <div class="warning-subtitle">Bestätige jetzt deine E-Mail-Adresse</div>
        </div>

        <div class="content">
            <p>Hallo <strong>{{ $user->name }}</strong>,</p>

            <p>dein THW-Trainer Account wurde am <strong>{{ $accountCreatedAt }}</strong> erstellt, aber deine E-Mail-Adresse wurde noch nicht bestätigt.</p>

            <div class="highlight">
                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #92400e;">
                    ⏰ Dein Account wird in <strong>2 Tagen automatisch gelöscht</strong>, 
                    wenn du deine E-Mail-Adresse nicht bestätigst!
                </p>
            </div>

            <p>Um deinen Account zu behalten und mit dem Lernen zu beginnen, klicke einfach auf den Button unten:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="cta-button">
                    📧 E-Mail-Adresse jetzt bestätigen
                </a>
            </div>

            <div class="account-info">
                <p style="margin: 0;"><strong>Account-Details:</strong></p>
                <p style="margin: 5px 0 0 0;">📧 E-Mail: {{ $user->email }}</p>
                <p style="margin: 5px 0 0 0;">📅 Erstellt: {{ $accountCreatedAt }}</p>
            </div>

            <p>Nach der Bestätigung kannst du:</p>
            <ul>
                <li>🏆 An THW-Prüfungsfragen üben</li>
                <li>📊 Deinen Lernfortschritt verfolgen</li>
                <li>🎯 Prüfungs-Simulationen absolvieren</li>
                <li>⭐ Fragen als Favoriten markieren</li>
            </ul>

            <p><strong>Falls du den Account nicht benötigst:</strong> Du musst nichts tun. Er wird automatisch gelöscht.</p>
        </div>

        <div class="footer">
            <p>Diese E-Mail wurde automatisch generiert. Bitte antworte nicht darauf.</p>
            <p>Bei Fragen kontaktiere uns über die Website.</p>
            <p style="margin-top: 15px; color: #9ca3af; font-size: 12px;">
                © {{ date('Y') }} THW-Trainer.de - Dein Partner für die THW-Prüfung
            </p>
        </div>
    </div>
</body>
</html>
