<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung bestätigen - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="https://{{ config('domains.landing') }}/logo-thwtrainer_w.png" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Registrierung</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Willkommen beim THW-Trainer!</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">Vielen Dank für deine Registrierung! Um dein Konto zu aktivieren, gib den folgenden Code auf der Bestätigungsseite ein:</p>

                <!-- Verifikationscode -->
                <div style="text-align:center;margin:24px 0;">
                    <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;border:1px solid rgba(0,51,127,0.12);display:inline-block;">
                        <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Dein Bestätigungscode</div>
                        <div style="font-size:32px;font-weight:800;color:#00337F;letter-spacing:6px;font-variant-numeric:tabular-nums;">{{ $verificationCode }}</div>
                    </div>
                </div>

                <!-- Gültigkeit -->
                <div style="background:#fffbeb;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #f59e0b;">
                    <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">Dieser Code ist <strong>15 Minuten gültig</strong>. Gib ihn schnell ein, um deinen Account zu aktivieren.</p>
                </div>

                <!-- Vorteile -->
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <p style="margin:0 0 8px 0;font-size:13px;font-weight:700;color:#1e3a5f;line-height:1.6;">Nach der Bestätigung kannst du:</p>
                    <ul style="margin:0;padding-left:18px;font-size:13px;color:#1e3a5f;line-height:1.8;">
                        <li>Deinen Lernfortschritt speichern</li>
                        <li>Prüfungssimulationen durchführen</li>
                        <li>Deine Statistiken einsehen</li>
                    </ul>
                </div>

                <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">Falls du dich nicht bei THW-Trainer registriert hast, kannst du diese E-Mail ignorieren.</p>

            </div>
            <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;">
                <div style="text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:11px;color:#94a3b8;line-height:1.6;"><strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung</p>
                    <p style="margin:0 0 6px 0;font-size:11px;color:#94a3b8;"><a href="https://{{ config('domains.landing') }}/impressum" style="color:#94a3b8;text-decoration:none;">Impressum</a> &middot; <a href="https://{{ config('domains.landing') }}/datenschutz" style="color:#94a3b8;text-decoration:none;">Datenschutz</a></p>
                    <p style="margin:0;font-size:11px;color:#cbd5e1;"><a href="https://{{ config('domains.app') }}/profile" style="color:#64748b;text-decoration:none;">E-Mail-Einstellungen ändern</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
