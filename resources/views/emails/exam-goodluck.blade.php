<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viel Erfolg bei deiner Prüfung - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background:#f8fafc;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:40px 32px;">

            <!-- Logo -->
            <div style="text-align:center;margin-bottom:24px;">
                <img src="https://thw-trainer.de/logo-thwtrainer.png" alt="THW-Trainer Logo" style="max-width:200px;height:auto;" />
            </div>

            <!-- Überschrift -->
            <h1 style="font-size:26px;font-weight:700;margin:0 0 24px 0;color:#003399;text-align:center;">
                Morgen ist es soweit!
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <!-- Motivationsbox -->
            <div style="background:linear-gradient(135deg,#eff6ff,#fef9e7);border:2px solid #FFD700;border-radius:12px;padding:28px;margin:20px 0;text-align:center;">
                <div style="font-size:20px;font-weight:700;color:#003399;margin-bottom:12px;">
                    Viel Erfolg bei deiner Prüfung!
                </div>
                <p style="margin:0;font-size:16px;color:#4b5563;line-height:1.6;">
                    Du hast dich mit dem THW Trainer intensiv vorbereitet und das Wissen sitzt.
                    Geh morgen selbstbewusst in die Prüfung - du schaffst das!
                </p>
            </div>

            <!-- Tipps -->
            <div style="background:#f9fafb;border-radius:8px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px 0;font-size:16px;font-weight:600;color:#1a202c;">
                    Tipps für morgen
                </p>
                <table style="width:100%;font-size:15px;color:#4b5563;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:8px 0;vertical-align:top;width:24px;color:#003399;font-weight:700;">1.</td>
                        <td style="padding:8px 0;">Lies jede Frage sorgfältig und vollständig durch</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;vertical-align:top;width:24px;color:#003399;font-weight:700;">2.</td>
                        <td style="padding:8px 0;">Achte auf Schlüsselwörter wie "immer", "nie", "ausschließlich"</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;vertical-align:top;width:24px;color:#003399;font-weight:700;">3.</td>
                        <td style="padding:8px 0;">Überspringe schwierige Fragen und komm später darauf zurück</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;vertical-align:top;width:24px;color:#003399;font-weight:700;">4.</td>
                        <td style="padding:8px 0;">Vertrau auf dein Wissen - du bist gut vorbereitet!</td>
                    </tr>
                </table>
            </div>

            <!-- Abschluss -->
            <p style="margin:24px 0;font-size:16px;color:#1a202c;line-height:1.6;">
                Das gesamte THW-Trainer-Team drückt dir die Daumen!
                Wir melden uns nach der Prüfung nochmal bei dir.
            </p>

            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:14px;color:#666;text-align:center;">
                    <strong>THW-Trainer</strong><br>
                    Dein persönlicher Lernbegleiter für die THW-Grundausbildung
                </p>
                <p style="margin:16px 0 0 0;font-size:13px;color:#888;text-align:center;">
                    Diese E-Mail wurde automatisch gesendet, weil du einen Prüfungstermin eingetragen hast.<br>
                    Du kannst deinen Prüfungstermin in deinem <a href="https://thw-trainer.de/profile" style="color:#003399;">Profil</a> ändern.
                </p>
            </div>

            <!-- Impressum -->
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
