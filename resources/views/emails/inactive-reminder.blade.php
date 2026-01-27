<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Du fehlst uns - THW Trainer</title>
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
                Hey {{ $user->name }}, du fehlst uns!
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <!-- Motivation -->
            <p style="margin:0 0 24px 0;font-size:18px;line-height:1.6;color:#1a202c;">
                Wir haben bemerkt, dass du seit {{ $daysInactive }} Tagen nicht mehr bei uns warst.
                Dein Wissen und dein Fortschritt warten auf dich!
            </p>

            @if($remainingQuestions > 0)
            <!-- Fortschritts-Box -->
            <div style="background:#eff6ff;border:2px solid #003399;border-radius:8px;padding:20px;margin:20px 0;text-align:center;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#003399;">
                    Dein Fortschritt
                </p>
                <p style="margin:10px 0;font-size:18px;color:#1a202c;">
                    Du hast bereits <strong>{{ $masteredQuestions }} von {{ $totalQuestions }} Fragen</strong> gemeistert!
                </p>
                <!-- Progress Bar -->
                <div style="background:#e5e7eb;border-radius:10px;height:30px;margin:15px 0;overflow:hidden;">
                    <div style="background:linear-gradient(to right, #22c55e, #16a34a);height:100%;width:{{ $progressPercentage }}%;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:14px;">
                        {{ $progressPercentage }}%
                    </div>
                </div>
                <p style="margin:15px 0 0 0;font-size:20px;font-weight:600;color:#16a34a;">
                    Nur noch <strong>{{ $remainingQuestions }} Frage{{ $remainingQuestions == 1 ? '' : 'n' }}</strong> bis zum Ziel!
                </p>
                <p style="margin:10px 0 0 0;font-size:12px;color:#6b7280;">
                    <em>Der Balken zeigt deinen Gesamt-Fortschritt inkl. teilweise gelöster Fragen</em>
                </p>
            </div>

            <!-- Stats Grid -->
            <table style="width:100%;margin:20px 0;border-collapse:separate;border-spacing:15px 0;">
                <tr>
                    <td style="background:#f3f4f6;padding:15px;border-radius:8px;text-align:center;width:50%;">
                        <div style="font-size:12px;color:#6b7280;">Gemeistert</div>
                        <div style="font-size:32px;font-weight:bold;color:#003399;margin:10px 0;">{{ $masteredQuestions }}</div>
                        <div style="font-size:12px;color:#6b7280;">von {{ $totalQuestions }} Fragen</div>
                    </td>
                    <td style="background:#f3f4f6;padding:15px;border-radius:8px;text-align:center;width:50%;">
                        <div style="font-size:12px;color:#6b7280;">Noch offen</div>
                        <div style="font-size:32px;font-weight:bold;color:#003399;margin:10px 0;">{{ $remainingQuestions }}</div>
                        <div style="font-size:12px;color:#6b7280;">Fragen</div>
                    </td>
                </tr>
            </table>

            <!-- Motivation Box -->
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:20px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#92400e;">
                    Du bist so nah dran!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#1a202c;">
                    @if($remainingQuestions <= 10)
                        Nur noch {{ $remainingQuestions }} Frage{{ $remainingQuestions == 1 ? '' : 'n' }}! Das schaffst du locker in ein paar Minuten.
                    @elseif($remainingQuestions <= 50)
                        Mit nur 10 Fragen pro Tag bist du in wenigen Tagen durch! Du packst das!
                    @else
                        Schritt für Schritt kommst du ans Ziel. Jede Frage bringt dich weiter!
                    @endif
                </p>
            </div>
            @else
            <!-- 100% geschafft -->
            <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:8px;padding:20px;margin:20px 0;text-align:center;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#16a34a;">
                    Alle Fragen gemeistert!
                </p>
                <p style="margin:10px 0;font-size:18px;color:#1a202c;">
                    Du hast bereits <strong>alle {{ $totalQuestions }} Fragen</strong> gemeistert!
                </p>
                <!-- Progress Bar 100% -->
                <div style="background:#e5e7eb;border-radius:10px;height:30px;margin:15px 0;overflow:hidden;">
                    <div style="background:linear-gradient(to right, #22c55e, #16a34a);height:100%;width:100%;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:14px;">
                        100%
                    </div>
                </div>
            </div>

            <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:8px;padding:18px;margin:20px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#16a34a;">
                    Bleib dran!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#1a202c;">
                    Wiederhole deine Fragen regelmäßig, um dein Wissen frisch zu halten.
                    Übung macht den Meister!
                </p>
            </div>
            @endif

            @if($user->points > 0)
            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                Du hast bereits <strong>{{ $user->points }} Punkte</strong> gesammelt und bist auf Level <strong>{{ $user->level }}</strong>.
                Lass uns zusammen weiter an deinem Erfolg arbeiten!
            </p>
            @endif

            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://thw-trainer.de/practice-menu" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Jetzt weiterlernen
                </a>
            </div>

            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;text-align:center;">
                @if($remainingQuestions > 0)
                    Komm zurück und beende, was du begonnen hast. Wir glauben an dich!
                @else
                    Bleib am Ball und halte dein Wissen frisch. Du bist großartig!
                @endif
            </p>

            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:14px;color:#666;text-align:center;">
                    <strong>THW-Trainer</strong><br>
                    Dein persönlicher Lernbegleiter für die THW-Grundausbildung
                </p>
                <p style="margin:16px 0 0 0;font-size:13px;color:#888;text-align:center;">
                    Diese E-Mail wurde automatisch gesendet, weil du {{ $daysInactive }} Tage inaktiv warst und E-Mail-Benachrichtigungen aktiviert hast.<br>
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
