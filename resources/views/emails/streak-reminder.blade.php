<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak Erinnerung - THW Trainer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .streak-icon {
            font-size: 48px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(to right, #2563eb, #1d4ed8);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }
        .cta-button:hover {
            background: linear-gradient(to right, #1d4ed8, #1e40af);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .warning-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .motivation-text {
            font-size: 18px;
            color: #1f2937;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">THW-Trainer</div>
            <div class="streak-icon">🔥</div>
            <h1>Dein Streak ist in Gefahr!</h1>
        </div>

        <p>Hallo {{ $user->name }},</p>

        <div class="warning-box">
            <h2 style="color: #d97706; margin-top: 0;">⚠️ Streak-Alarm!</h2>
            <p>Du hast heute noch nicht gelernt und dein <strong>{{ $streakDays }}-Tage Streak</strong> ist in Gefahr!</p>
        </div>

        <div class="motivation-text">
            Du warst schon {{ $streakDays }} Tage in Folge aktiv - das ist fantastisch! 
            Lass uns diesen Streak nicht unterbrechen!
        </div>

        <p>Mit nur einer Frage heute kannst du deinen Streak retten und weiter auf dein nächstes Achievement hinarbeiten.</p>

        <div style="text-align: center;">
            <a href="https://thw-trainer.de/practice-menu" class="cta-button">
                🚀 Jetzt lernen und Streak retten!
            </a>
        </div>

        <div class="motivation-text">
            Du schaffst das! Ein kurzer Lernmoment heute und dein Streak bleibt erhalten.
        </div>

        <div class="footer">
            <p>
                <strong>THW-Trainer</strong><br>
                Dein persönlicher Lernbegleiter für die THW-Grundausbildung
            </p>
            <p>
                Diese E-Mail wurde automatisch gesendet, weil du E-Mail-Benachrichtigungen aktiviert hast.<br>
                Du kannst diese Einstellung in deinem <a href="https://thw-trainer.de/profile">Profil</a> ändern.
            </p>
        </div>
    </div>
</body>
</html>
