<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prüfungs-Erinnerung - THW Trainer</title>
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
                Dein täglicher Lernplan
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <!-- Countdown-Box -->
            <div style="background:#eff6ff;border:2px solid #003399;border-radius:8px;padding:20px;margin:18px 0;text-align:center;">
                <div style="font-size:48px;font-weight:800;color:#003399;line-height:1;">{{ $daysLeft }}</div>
                <div style="font-size:16px;font-weight:600;color:#003399;margin-top:4px;">
                    Tag{{ $daysLeft != 1 ? 'e' : '' }} bis zur Prüfung
                </div>
            </div>

            @if($todayRemaining > 0)
            <!-- Heutiges Pensum -->
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#92400e;">
                    Dein Tagespensum
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#92400e;">
                    Du musst heute noch <strong>{{ $todayRemaining }} Fragen</strong> beantworten, um auf Kurs zu bleiben.
                    @if($todayAnswered > 0)
                        ({{ $todayAnswered }} von {{ $dailyTarget }} bereits geschafft)
                    @endif
                </p>
            </div>
            @else
            <!-- Tagesziel erreicht -->
            <div style="background:#dcfce7;border:2px solid #22c55e;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#166534;">
                    Tagesziel erreicht!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#166534;">
                    Du hast heute bereits <strong>{{ $todayAnswered }} Fragen</strong> beantwortet und dein Tagesziel von {{ $dailyTarget }} geschafft.
                </p>
            </div>
            @endif

            <!-- Fortschritts-Übersicht -->
            <div style="background:#f9fafb;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0 0 12px 0;font-size:16px;font-weight:600;color:#1a202c;">
                    Dein Fortschritt
                </p>
                <!-- Fortschrittsbalken -->
                <div style="background:#e5e7eb;border-radius:4px;height:8px;overflow:hidden;margin-bottom:8px;">
                    <div style="height:100%;width:{{ $progressPercent }}%;background:linear-gradient(90deg,#003399,#FFD700);border-radius:4px;"></div>
                </div>
                <table style="width:100%;font-size:14px;color:#4b5563;" cellpadding="4" cellspacing="0">
                    <tr>
                        <td style="padding:4px 0;">Gemeisterte Fragen:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $masteredCount }} / {{ $totalQuestions }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;">Gesamtfortschritt:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $progressPercent }}%</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;">Empfohlenes Tagespensum:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $dailyTarget }} Fragen/Tag</td>
                    </tr>
                </table>
            </div>

            @if($daysLeft <= 7)
            <!-- Letzte-Woche-Warnung -->
            <div style="background:#fef2f2;border:2px solid #ef4444;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#991b1b;">
                    Letzte Woche vor der Prüfung!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#991b1b;">
                    Nutze die verbleibenden Tage, um Prüfungssimulationen durchzuführen und deine Schwächen gezielt zu trainieren.
                </p>
            </div>
            @elseif($daysLeft <= 14)
            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                In zwei Wochen ist es soweit! Bleib am Ball und arbeite dein tägliches Pensum konsequent ab.
            </p>
            @else
            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                Du bist auf einem guten Weg! Bleib dran und lerne jeden Tag ein bisschen weiter.
            </p>
            @endif

            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://thw-trainer.de/practice-menu" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Jetzt lernen
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
