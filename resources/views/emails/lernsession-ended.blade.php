<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernsession beendet - THW Trainer</title>
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
                @if($isWinner)
                    Herzlichen Glückwunsch!
                @else
                    Lernsession beendet
                @endif
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            @if($isWinner)
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Du hast die Lernsession <strong>{{ $session->title }}</strong> gewonnen!
            </p>
            @else
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Die Lernsession <strong>{{ $session->title }}</strong> ist beendet. Hier sind deine Ergebnisse:
            </p>
            @endif

            <!-- Platzierung -->
            <div style="background:{{ $isWinner ? '#fef3c7' : '#eff6ff' }};border:2px solid {{ $isWinner ? '#f59e0b' : '#003399' }};border-radius:8px;padding:20px;margin:18px 0;text-align:center;">
                <div style="font-size:48px;font-weight:800;color:{{ $isWinner ? '#92400e' : '#003399' }};line-height:1;">
                    Platz {{ $participant->final_rank }}
                </div>
                <div style="font-size:16px;font-weight:600;color:{{ $isWinner ? '#92400e' : '#003399' }};margin-top:4px;">
                    von {{ $totalParticipants }} Teilnehmer{{ $totalParticipants != 1 ? 'n' : '' }}
                </div>
            </div>

            <!-- Statistiken -->
            <div style="background:#f9fafb;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0 0 12px 0;font-size:16px;font-weight:600;color:#1a202c;">
                    Deine Statistiken
                </p>
                <table style="width:100%;font-size:14px;color:#4b5563;" cellpadding="4" cellspacing="0">
                    <tr>
                        <td style="padding:4px 0;">Fragen beantwortet:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $participant->questions_answered }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;">Richtige Antworten:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $participant->questions_correct }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;">Genauigkeit:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ number_format($participant->accuracy_percent, 1) }}%</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;">Verdiente XP:</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $participant->xp_earned }} XP</td>
                    </tr>
                </table>
            </div>

            @if($isWinner && $hasLootbox)
            <!-- Belohnungskiste -->
            <div style="background:#dcfce7;border:2px solid #22c55e;border-radius:8px;padding:18px;margin:18px 0;">
                <p style="margin:0;font-size:16px;font-weight:600;color:#166534;">
                    Gold-Belohnungskiste erhalten!
                </p>
                <p style="margin:8px 0 0 0;font-size:15px;color:#166534;">
                    Als Gewinner der Lernsession hast du eine Gold-Belohnungskiste erhalten. Öffne sie im Dashboard, um deine Belohnung zu entdecken!
                </p>
            </div>
            @endif

            <!-- Motivation -->
            <p style="margin:20px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                @if($isWinner)
                    Großartige Leistung! Halte dein Niveau und nimm an weiteren Lernsessions teil.
                @elseif($participant->final_rank <= 3)
                    Tolle Leistung! Du warst ganz nah dran am Sieg. Beim nächsten Mal schaffst du es!
                @else
                    Gut gemacht! Übung macht den Meister - nimm an weiteren Lernsessions teil, um dich zu verbessern.
                @endif
            </p>

            <!-- Call-to-Action Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://thw-trainer.de/lernsessions" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Weitere Lernsessions anzeigen
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
