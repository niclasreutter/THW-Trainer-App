<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        /* Custom Komponenten */
        .info-card {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);
        }
        .warning-card {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);
        }
        .success-card {
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);
        }
        .error-card {
            background-color: #fef2f2;
            border: 2px solid #ef4444;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);
        }
        .glow-button {
            display: inline-block !important;
            background: linear-gradient(to right, #2563eb, #1d4ed8) !important;
            color: #ffffff !important;
            padding: 15px 30px !important;
            text-decoration: none !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            margin: 20px 0 !important;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1) !important;
        }
        .glow-button:hover {
            background: linear-gradient(to right, #1d4ed8, #1e40af) !important;
        }
        a.glow-button {
            color: #ffffff !important;
        }
        .stat-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            /* text-align wird via Inline-Style gesetzt */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">THW-Trainer</div>
        </div>

        <!-- Newsletter Content -->
        {!! $htmlContent !!}

        <div class="footer">
            <p>
                <strong>THW-Trainer</strong><br>
                Dein persönlicher Lernbegleiter für die THW-Grundausbildung
            </p>
            <p>
                Du erhältst diese E-Mail, weil du E-Mail-Benachrichtigungen aktiviert hast.<br>
                Du kannst diese Einstellung in deinem <a href="https://thw-trainer.de/profile">Profil</a> ändern.
            </p>
        </div>
    </div>
</body>
</html>

