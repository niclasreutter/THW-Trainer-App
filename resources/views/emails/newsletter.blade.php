<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Custom Komponenten für Newsletter-Content */
        .info-card {
            background:#f0f4ff;
            border-left:3px solid #3b82f6;
            border-radius:8px;
            padding:14px 16px;
            margin:16px 0;
            color:#1e3a5f;
            font-size:13px;
            line-height:1.6;
        }
        .info-card p { margin:0 0 8px 0; }
        .info-card p:last-child { margin-bottom:0; }

        .warning-card {
            background:#fffbeb;
            border-left:3px solid #f59e0b;
            border-radius:8px;
            padding:14px 16px;
            margin:16px 0;
            color:#92400e;
            font-size:13px;
            line-height:1.6;
        }
        .warning-card p { margin:0 0 8px 0; }
        .warning-card p:last-child { margin-bottom:0; }

        .success-card {
            background:#f0fdf4;
            border-left:3px solid #22c55e;
            border-radius:8px;
            padding:14px 16px;
            margin:16px 0;
            color:#166534;
            font-size:13px;
            line-height:1.6;
        }
        .success-card p { margin:0 0 8px 0; }
        .success-card p:last-child { margin-bottom:0; }

        .error-card {
            background:#fef2f2;
            border-left:3px solid #ef4444;
            border-radius:8px;
            padding:14px 16px;
            margin:16px 0;
            color:#991b1b;
            font-size:13px;
            line-height:1.6;
        }
        .error-card p { margin:0 0 8px 0; }
        .error-card p:last-child { margin-bottom:0; }

        .glow-button {
            display:inline-block !important;
            background:linear-gradient(135deg,#00337F,#0055cc) !important;
            color:#ffffff !important;
            padding:12px 32px !important;
            text-decoration:none !important;
            border-radius:0.5rem !important;
            font-weight:700 !important;
            font-size:13px !important;
            margin:16px 0 !important;
            box-shadow:0 4px 15px rgba(0,51,127,0.3) !important;
        }
        a.glow-button { color:#ffffff !important; }

        .stat-box {
            background:#f0f4ff;
            border:1px solid rgba(0,51,127,0.12);
            border-radius:2rem;
            padding:14px 18px;
            margin:12px 0;
            text-align:center;
        }
        .stat-number {
            font-size:24px;
            font-weight:800;
            color:#00337F;
            margin:4px 0;
            font-variant-numeric:tabular-nums;
        }
        .stat-label {
            font-size:11px;
            font-weight:700;
            letter-spacing:0.08em;
            text-transform:uppercase;
            color:#64748b;
        }

        /* Typografie für eingebetteten Inhalt */
        .newsletter-content h1,
        .newsletter-content h2 {
            font-size:18px;
            font-weight:800;
            color:#0f172a;
            margin:20px 0 10px 0;
            line-height:1.3;
        }
        .newsletter-content h3 {
            font-size:15px;
            font-weight:700;
            color:#0f172a;
            margin:16px 0 8px 0;
            line-height:1.3;
        }
        .newsletter-content p {
            margin:0 0 12px 0;
            font-size:13px;
            color:#475569;
            line-height:1.6;
        }
        .newsletter-content ul,
        .newsletter-content ol {
            margin:0 0 12px 0;
            padding-left:20px;
            font-size:13px;
            color:#475569;
            line-height:1.7;
        }
        .newsletter-content a {
            color:#00337F;
            text-decoration:underline;
        }
        .newsletter-content strong { color:#0f172a; }
    </style>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">

            <!-- Header -->
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

            <!-- Body -->
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Subject -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Newsletter</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">{{ $subject }}</div>

                <!-- Newsletter Content -->
                <div class="newsletter-content" style="color:#475569;font-size:13px;line-height:1.6;">
                    {!! $htmlContent !!}
                </div>

            </div>

            <!-- Footer -->
            <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;">
                <div style="text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:11px;color:#94a3b8;line-height:1.6;">
                        <strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung
                    </p>
                    <p style="margin:0 0 6px 0;font-size:11px;color:#94a3b8;">
                        <a href="https://{{ config('domains.landing') }}/impressum" style="color:#94a3b8;text-decoration:none;">Impressum</a> &middot;
                        <a href="https://{{ config('domains.landing') }}/datenschutz" style="color:#94a3b8;text-decoration:none;">Datenschutz</a>
                    </p>
                    <p style="margin:0;font-size:11px;color:#cbd5e1;">
                        <a href="https://{{ config('domains.app') }}/profile" style="color:#64748b;text-decoration:none;">E-Mail-Einstellungen ändern</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
