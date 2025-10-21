<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail-Bestätigung - THW-Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background:#f8fafc;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:40px 32px;">
            
            <!-- Logo -->
            <div style="text-align:center;margin-bottom:24px;">
                <img src="https://thw-trainer.de/logo-thwtrainer.png" alt="THW-Trainer Logo" style="max-width:200px;height:auto;" />
            </div>
            
            <!-- Überschrift -->
            <h1 style="font-size:24px;font-weight:600;margin:0 0 24px 0;color:#003399;text-align:center;">
                Willkommen beim THW-Trainer!
            </h1>
            
            <!-- Haupttext -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Vielen Dank für deine Registrierung beim THW-Trainer!
            </p>
            
            <p style="margin:0 0 24px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Um dein Konto zu aktivieren und alle Funktionen nutzen zu können, bestätige bitte deine E-Mail-Adresse mit einem Klick auf den Button:
            </p>
            
            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="{{ $verificationUrl }}" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    E-Mail-Adresse bestätigen
                </a>
            </div>
            
            <!-- Alternativ-Link -->
            <p style="margin:24px 0 0 0;font-size:14px;line-height:1.6;color:#666;text-align:center;">
                Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:
            </p>
            <p style="margin:8px 0 24px 0;font-size:13px;line-height:1.4;color:#0066cc;word-break:break-all;text-align:center;">
                {{ $verificationUrl }}
            </p>
            
            <!-- Vorteile -->
            <div style="background:#f8fafc;border-left:4px solid #003399;padding:16px 20px;margin:24px 0;">
                <p style="margin:0 0 12px 0;font-weight:600;color:#003399;font-size:15px;">Nach der Bestätigung kannst du:</p>
                <ul style="margin:0;padding-left:20px;color:#1a202c;line-height:1.8;">
                    <li>Deinen Lernfortschritt speichern</li>
                    <li>Prüfungssimulationen durchführen</li>
                    <li>Deine Statistiken einsehen</li>
                </ul>
            </div>
            
            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:13px;color:#666;line-height:1.5;">
                    <strong>Hinweis:</strong> Falls du dich nicht bei THW-Trainer registriert hast, kannst du diese E-Mail ignorieren.
                </p>
                <p style="margin:16px 0 0 0;font-size:14px;color:#666;text-align:center;">
                    Viele Grüße<br>
                    <strong>Dein THW-Trainer Team</strong>
                </p>
            </div>
            
            <!-- Impressum/Kontakt -->
            <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;text-align:center;">
                <p style="margin:0;font-size:12px;color:#999;line-height:1.5;">
                    © {{ date('Y') }} THW-Trainer.de | 
                    <a href="https://thw-trainer.de/impressum" style="color:#999;text-decoration:none;">Impressum</a> | 
                    <a href="https://thw-trainer.de/datenschutz" style="color:#999;text-decoration:none;">Datenschutz</a>
                </p>
            </div>
            
        </div>
    </div>
</body>
</html>
