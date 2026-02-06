<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiederholung fällig - THW Trainer</title>
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
                Wiederholungen fällig
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <!-- Info-Box -->
            <div style="background:#eff6ff;border:2px solid #003399;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#003399;">
                    Spaced Repetition
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#1a202c;">
                    {{ $dueCount }} {{ $dueCount === 1 ? 'Frage wartet' : 'Fragen warten' }} auf deine Wiederholung.
                    Regelmäßiges Wiederholen sorgt dafür, dass du das Gelernte langfristig behältst.
                </p>
            </div>

            <!-- Motivation -->
            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                Dein Gehirn vergisst gelernte Inhalte mit der Zeit. Durch gezieltes Wiederholen zum richtigen
                Zeitpunkt verankerst du das Wissen dauerhaft. Je länger du wartest, desto mehr musst du
                später nachholen.
            </p>

            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://thw-trainer.de/practice/spaced-repetition" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Jetzt {{ $dueCount }} {{ $dueCount === 1 ? 'Frage' : 'Fragen' }} wiederholen
                </a>
            </div>

            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:14px;color:#666;text-align:center;">
                    <strong>THW-Trainer</strong><br>
                    Dein persönlicher Lernbegleiter für die THW-Grundausbildung
                </p>
                <p style="margin:16px 0 0 0;font-size:13px;color:#888;text-align:center;">
                    Diese E-Mail wurde automatisch gesendet, weil du E-Mail-Benachrichtigungen aktiviert hast.<br>
                    Du kannst diese Einstellung in deinem <a href="https://thw-trainer.de/profile" style="color:#003399;">Profil</a> ändern.
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
