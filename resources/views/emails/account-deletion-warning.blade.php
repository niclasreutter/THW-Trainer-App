<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account-Löschung Warnung - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <tr>
                        <td width="32" height="32" align="center" valign="middle" style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;text-align:center;vertical-align:middle;">
                            <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW" width="18" height="18" style="display:block;margin:0 auto;width:18px;height:18px;border:0;">
                        </td>
                        <td valign="middle" style="vertical-align:middle;padding-left:10px;color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</td>
                    </tr>
                </table>
            </div>
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Achtung</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Account-Löschung in 2 Tagen!</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">Hallo <strong>{{ $user->name }}</strong>,</p>
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">dein THW-Trainer Account wurde am <strong>{{ $accountCreatedAt }}</strong> erstellt, aber deine E-Mail-Adresse wurde noch nicht bestätigt.</p>

                <!-- Fehler-Box: Löschwarnung -->
                <div style="background:#fef2f2;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #ef4444;">
                    <p style="margin:0;font-size:13px;color:#991b1b;line-height:1.6;">Dein Account wird in <strong>2 Tagen automatisch gelöscht</strong>, wenn du deine E-Mail-Adresse nicht bestätigst!</p>
                </div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">Um deinen Account zu behalten und mit dem Lernen zu beginnen, klicke einfach auf den Button unten:</p>

                <!-- Account-Details -->
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <p style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#1e3a5f;">Account-Details:</p>
                    <p style="margin:0 0 4px 0;font-size:13px;color:#1e3a5f;line-height:1.6;">E-Mail: {{ $user->email }}</p>
                    <p style="margin:0;font-size:13px;color:#1e3a5f;line-height:1.6;">Erstellt: {{ $accountCreatedAt }}</p>
                </div>

                <!-- CTA -->
                <div style="text-align:center;margin:24px 0;">
                    <a href="{{ $verificationUrl }}" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">E-Mail-Adresse jetzt bestätigen</a>
                </div>

                <!-- Vorteile nach Bestätigung -->
                <div style="background:#f0fdf4;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #22c55e;">
                    <p style="margin:0 0 8px 0;font-size:13px;font-weight:700;color:#166534;">Nach der Bestätigung kannst du:</p>
                    <ul style="margin:0;padding-left:18px;font-size:13px;color:#166534;line-height:1.8;">
                        <li>An THW-Prüfungsfragen üben</li>
                        <li>Deinen Lernfortschritt verfolgen</li>
                        <li>Prüfungs-Simulationen absolvieren</li>
                        <li>Fragen als Favoriten markieren</li>
                    </ul>
                </div>

                <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">Falls du den Account nicht benötigst, musst du nichts tun. Er wird automatisch gelöscht.</p>

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
