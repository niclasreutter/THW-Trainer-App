<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wie lief deine Prüfung? - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background:#f8fafc;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:40px 32px;">

            <!-- Logo -->
            <div style="text-align:center;margin-bottom:24px;">
                <img src="https://thw-trainer.de/logo-thwtrainer.png" alt="THW-Trainer Logo" style="max-width:200px;height:auto;" />
            </div>

            <!-- Überschrift -->
            <h1 style="font-size:24px;font-weight:700;margin:0 0 24px 0;color:#003399;text-align:center;">
                Wie lief deine Prüfung?
            </h1>

            <!-- Anrede -->
            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Hallo <strong>{{ $user->name }}</strong>,
            </p>

            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1a202c;">
                Deine Prüfung liegt jetzt eine Woche zurück und wir sind gespannt, wie es gelaufen ist!
                Wir würden uns sehr freuen, wenn du dir kurz Zeit nimmst und uns dein Feedback gibst.
            </p>

            <!-- Warum Feedback -->
            <div style="background:#eff6ff;border-left:4px solid #003399;border-radius:4px;padding:16px 20px;margin:20px 0;">
                <p style="margin:0;font-size:15px;color:#1a202c;line-height:1.6;">
                    <strong>Warum dein Feedback wichtig ist:</strong><br>
                    Deine Rückmeldung hilft anderen THW-Helfern zu sehen, wie gut die Vorbereitung mit dem THW Trainer funktioniert.
                    Außerdem erheben wir die Bestehens- und Durchfallquote, um die Wirksamkeit unserer Plattform transparent zu zeigen.
                </p>
            </div>

            <!-- Call-to-Action -->
            <div style="text-align:center;margin:32px 0;">
                <a href="{{ $feedbackUrl }}" style="background:#FFD700;color:#003399;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;display:inline-block;">
                    Feedback geben
                </a>
            </div>

            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#6b7280;text-align:center;">
                Der Fragebogen dauert weniger als eine Minute.
            </p>

            <!-- Footer -->
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;font-size:14px;color:#666;text-align:center;">
                    <strong>THW-Trainer</strong><br>
                    Dein persönlicher Lernbegleiter für die THW-Grundausbildung
                </p>
                <p style="margin:16px 0 0 0;font-size:13px;color:#888;text-align:center;">
                    Diese E-Mail wurde automatisch gesendet, weil du einen Prüfungstermin eingetragen hattest.<br>
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
