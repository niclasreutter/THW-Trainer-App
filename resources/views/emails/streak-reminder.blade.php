<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak Erinnerung - THW Trainer</title>
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
                Dein Streak ist in Gefahr!
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <!-- Warnung -->
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#92400e;">
                    Streak-Alarm!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#92400e;">
                    Du hast heute noch nicht gelernt und dein <strong>{{ $streakDays }}-Tage Streak</strong> ist in Gefahr!
                </p>
            </div>

            <!-- Motivation -->
            <p style="margin:20px 0;font-size:18px;color:#1a202c;line-height:1.6;">
                Du warst schon {{ $streakDays }} Tage in Folge aktiv - das ist fantastisch!
                Lass uns diesen Streak nicht unterbrechen!
            </p>

            @if($streakDays >= 3)
            <!-- Streak-Bonus aktiv -->
            <div style="background:#eff6ff;border:2px solid #003399;border-radius:8px;padding:18px;margin:20px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#003399;">
                    Streak-Bonus aktiv!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#1a202c;">
                    Da du bereits {{ $streakDays }} Tage Streak hast, bekommst du <strong>doppelte Punkte</strong> für jede richtige Antwort!
                    Das sind 20 Punkte statt 10 Punkte pro Frage - oder willst du wieder nur 10 Punkte bekommen?
                </p>
            </div>
            @else
            <!-- Noch kein Bonus -->
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:20px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#92400e;">
                    Nur noch {{ 3 - $streakDays }} Tag{{ 3 - $streakDays == 1 ? '' : 'e' }} bis zum Streak-Bonus!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#1a202c;">
                    Lerne heute und morgen weiter, dann bekommst du ab dem {{ $streakDays + 1 }}. Tag <strong>doppelte Punkte</strong> für jede richtige Antwort!
                    Das sind 20 Punkte statt 10 Punkte pro Frage - willst du dir das entgehen lassen?
                </p>
            </div>
            @endif

            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                Mit nur einer Frage heute kannst du deinen Streak retten und weiter auf dein nächstes Achievement hinarbeiten.
            </p>

            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://thw-trainer.de/practice-menu" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Jetzt lernen und Streak retten
                </a>
            </div>

            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;text-align:center;">
                Du schaffst das! Ein kurzer Lernmoment heute und dein Streak bleibt erhalten.
            </p>

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
