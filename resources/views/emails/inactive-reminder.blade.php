<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Du fehlst uns - THW Trainer</title>
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
        .welcome-back-icon {
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
        .progress-box {
            background-color: #dbeafe;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .progress-bar-container {
            background-color: #e5e7eb;
            border-radius: 10px;
            height: 30px;
            margin: 15px 0;
            overflow: hidden;
            position: relative;
        }
        .progress-bar {
            background: linear-gradient(to right, #10b981, #059669);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .motivation-text {
            font-size: 18px;
            color: #1f2937;
            margin: 20px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }
        .highlight-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">THW-Trainer</div>
            <div class="welcome-back-icon">👋</div>
            <h1>Hey {{ $user->name }}, du fehlst uns!</h1>
        </div>

        <p>Hallo {{ $user->name }},</p>

        <div class="motivation-text">
            Wir haben bemerkt, dass du seit {{ $daysInactive }} Tagen nicht mehr bei uns warst. 
            Dein Wissen und dein Fortschritt warten auf dich! 📚
        </div>

        @if($remainingQuestions > 0)
        <div class="progress-box">
            <h2 style="color: #1d4ed8; margin-top: 0;">🎯 Dein Fortschritt</h2>
            <p style="font-size: 18px; margin: 10px 0;">
                Du hast bereits <strong>{{ $masteredQuestions }} von {{ $totalQuestions }} Fragen</strong> gemeistert!
            </p>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: {{ $progressPercentage }}%;">
                    {{ $progressPercentage }}%
                </div>
            </div>
            <p style="color: #059669; font-weight: bold; font-size: 20px; margin-top: 15px;">
                Nur noch <strong>{{ $remainingQuestions }} Frage{{ $remainingQuestions == 1 ? '' : 'n' }}</strong> bis zum Ziel!
            </p>
            <p style="font-size: 12px; color: #6b7280; margin-top: 10px;">
                <em>Der Balken zeigt deinen Gesamt-Fortschritt inkl. teilweise gelöster Fragen</em>
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Gemeistert</div>
                <div class="stat-number">{{ $masteredQuestions }}</div>
                <div class="stat-label">von {{ $totalQuestions }} Fragen</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Noch offen</div>
                <div class="stat-number">{{ $remainingQuestions }}</div>
                <div class="stat-label">Fragen</div>
            </div>
        </div>

        <div class="highlight-box">
            <h3 style="color: #d97706; margin-top: 0;">💪 Du bist so nah dran!</h3>
            <p style="margin-bottom: 0;">
                @if($remainingQuestions <= 10)
                    Nur noch {{ $remainingQuestions }} Frage{{ $remainingQuestions == 1 ? '' : 'n' }}! Das schaffst du locker in ein paar Minuten. 🚀
                @elseif($remainingQuestions <= 50)
                    Mit nur 10 Fragen pro Tag bist du in wenigen Tagen durch! Du packst das! 💪
                @else
                    Schritt für Schritt kommst du ans Ziel. Jede Frage bringt dich weiter! 🎯
                @endif
            </p>
        </div>
        @else
        <div class="progress-box">
            <h2 style="color: #1d4ed8; margin-top: 0;">🎉 Alle Fragen gemeistert!</h2>
            <p style="font-size: 18px; margin: 10px 0;">
                Du hast bereits <strong>alle {{ $totalQuestions }} Fragen</strong> gemeistert!
            </p>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 100%;">
                    100%
                </div>
            </div>
        </div>

        <div class="highlight-box">
            <h3 style="color: #059669; margin-top: 0;">💡 Bleib dran!</h3>
            <p style="margin-bottom: 0;">
                Wiederhole deine Fragen regelmäßig, um dein Wissen frisch zu halten. 
                Übung macht den Meister! 🏆
            </p>
        </div>
        @endif

        @if($user->points > 0)
        <div class="motivation-text">
            Du hast bereits <strong>{{ $user->points }} Punkte</strong> gesammelt und bist auf Level <strong>{{ $user->level }}</strong>. 
            Lass uns zusammen weiter an deinem Erfolg arbeiten!
        </div>
        @endif

        <div style="text-align: center;">
            <a href="https://thw-trainer.de/practice-menu" class="cta-button">
                🚀 Jetzt weiterlernen!
            </a>
        </div>

        <div class="motivation-text">
            @if($remainingQuestions > 0)
                Komm zurück und beende, was du begonnen hast. Wir glauben an dich! 💙
            @else
                Bleib am Ball und halte dein Wissen frisch. Du bist großartig! 💙
            @endif
        </div>

        <div class="footer">
            <p>
                <strong>THW-Trainer</strong><br>
                Dein persönlicher Lernbegleiter für die THW-Grundausbildung
            </p>
            <p>
                Diese E-Mail wurde automatisch gesendet, weil du {{ $daysInactive }} Tage inaktiv warst und E-Mail-Benachrichtigungen aktiviert hast.<br>
                Du kannst diese Einstellung in deinem <a href="https://thw-trainer.de/profile">Profil</a> ändern.
            </p>
        </div>
    </div>
</body>
</html>

