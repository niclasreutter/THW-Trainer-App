<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account wurde gelöscht</title>
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
            border-bottom: 2px solid #dc2626;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .deleted-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .deleted-title {
            color: #dc2626;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .deleted-subtitle {
            color: #6b7280;
            font-size: 16px;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fca5a5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
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
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
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
            <div class="deleted-icon">❌</div>
            <div class="deleted-title">Account wurde gelöscht</div>
            <div class="deleted-subtitle">E-Mail-Adresse nicht bestätigt</div>
        </div>

        <div class="content">
            <p>Hallo <strong>{{ $user->name }}</strong>,</p>

            <p>dein THW-Trainer Account (erstellt am {{ $accountCreatedAt }}) wurde aufgrund fehlender E-Mail-Bestätigung automatisch gelöscht.</p>

            <div class="info-box">
                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #991b1b;">
                    🗑️ Dein Account und alle damit verbundenen Daten wurden permanent entfernt.
                </p>
            </div>

            <p>Das passiert, wenn ein Account länger als 9 Tage ohne E-Mail-Bestätigung existiert.</p>

            <h3>Möchtest du trotzdem bei THW-Trainer.de lernen?</h3>
            <p>Kein Problem! Du kannst jederzeit einen neuen Account erstellen:</p>

            <div style="text-align: center;">
                <a href="{{ url('/register') }}" class="cta-button">
                    🆕 Neuen Account erstellen
                </a>
            </div>

            <div class="account-info">
                <p style="margin: 0;"><strong>Warum wurde der Account gelöscht?</strong></p>
                <p style="margin: 5px 0 0 0;">📧 E-Mail-Adresse wurde nicht bestätigt</p>
                <p style="margin: 5px 0 0 0;">⏰ Account war länger als 9 Tage inaktiv</p>
                <p style="margin: 5px 0 0 0;">⚠️ Warnung wurde 2 Tage vorher gesendet</p>
            </div>

            <h3>Beim nächsten Mal:</h3>
            <ul>
                <li>✅ Bestätige deine E-Mail-Adresse direkt nach der Registrierung</li>
                <li>📧 Prüfe deinen Spam-Ordner, falls keine E-Mail ankommt</li>
                <li>🔄 Fordere eine neue Bestätigungs-E-Mail an, falls nötig</li>
            </ul>

            <p><strong>Hinweis:</strong> Alle deine Lernfortschritte und Einstellungen sind mit dem Account gelöscht worden. Ein neuer Account startet mit leeren Statistiken.</p>
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
