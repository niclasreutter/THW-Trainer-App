<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail bestätigen - THW Trainer</title>
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

                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">E-Mail Änderung</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">E-Mail-Adresse bestätigen</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">Hallo <strong>{{ $user->name }}</strong>,</p>
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">du hast deine E-Mail-Adresse in deinem THW-Trainer Profil geändert. Um die Änderung zu bestätigen, klicke bitte auf den Button unten.</p>

                <!-- Gültigkeit -->
                <div style="background:#fffbeb;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #f59e0b;">
                    <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;"><strong>Wichtig:</strong> Dieser Link ist nur für <strong>5 Minuten gültig</strong>.</p>
                </div>

                <!-- CTA -->
                <div style="text-align:center;margin:24px 0;">
                    <a href="{{ $verificationUrl }}" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">E-Mail-Adresse bestätigen</a>
                </div>

                <p style="margin:0 0 12px 0;font-size:12px;color:#94a3b8;line-height:1.6;">Falls du diese Änderung nicht vorgenommen hast, ignoriere diese E-Mail einfach.</p>
                <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br><span style="color:#64748b;word-break:break-all;">{{ $verificationUrl }}</span></p>

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
