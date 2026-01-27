<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background:#f8fafc;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:40px 32px;">

            <!-- Logo -->
            <div style="text-align:center;margin-bottom:24px;">
                <img src="https://thw-trainer.de/logo-thwtrainer.png" alt="THW-Trainer Logo" style="max-width:200px;height:auto;" />
            </div>

            <!-- Newsletter Content -->
            <div style="color:#1a202c;font-size:16px;line-height:1.6;">
                <style>
                    /* Custom Komponenten für Newsletter-Content */
                    .info-card {
                        background-color: #eff6ff;
                        border: 2px solid #003399;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                    }
                    .warning-card {
                        background-color: #fef3c7;
                        border: 2px solid #f59e0b;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                    }
                    .success-card {
                        background-color: #f0fdf4;
                        border: 2px solid #22c55e;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                    }
                    .error-card {
                        background-color: #fef2f2;
                        border: 2px solid #ef4444;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                    }
                    .glow-button {
                        display: inline-block !important;
                        background: #FFD700 !important;
                        color: #003399 !important;
                        padding: 14px 40px !important;
                        text-decoration: none !important;
                        border-radius: 8px !important;
                        font-weight: 600 !important;
                        margin: 20px 0 !important;
                    }
                    a.glow-button {
                        color: #003399 !important;
                    }
                    .stat-box {
                        background-color: #f3f4f6;
                        padding: 15px;
                        border-radius: 8px;
                        margin: 10px 0;
                    }
                    .stat-number {
                        font-size: 32px;
                        font-weight: bold;
                        color: #003399;
                        margin: 10px 0;
                    }
                    .stat-label {
                        font-size: 14px;
                        color: #6b7280;
                    }
                </style>

                {!! $htmlContent !!}
            </div>

            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:14px;color:#666;text-align:center;">
                    <strong>THW-Trainer</strong><br>
                    Dein persönlicher Lernbegleiter für die THW-Grundausbildung
                </p>
                <p style="margin:16px 0 0 0;font-size:13px;color:#888;text-align:center;">
                    Du erhältst diese E-Mail, weil du E-Mail-Benachrichtigungen aktiviert hast.<br>
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
