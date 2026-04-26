<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernsession gestartet - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Title -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Lernsession</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">{{ $session->title }}</div>

                <!-- Greeting -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $user->name }}</strong>,<br>
                    eine neue Lernsession wurde gerade gestartet und wartet auf dich!
                </p>

                @if($session->description)
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">{{ $session->description }}</p>
                @endif

                <!-- Stat Pills -->
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:12px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Endet um</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $instance->ends_at->format('H:i') }} Uhr</span>
                    </div>
                </div>
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:16px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Typ</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $session->isGlobal() ? 'Globale Session' : 'OV-Session' }}</span>
                    </div>
                </div>

                <!-- Info Box -->
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#1e3a5f;margin-bottom:4px;">XP &amp; Ranking</div>
                    <p style="margin:0;font-size:13px;color:#1e3a5f;line-height:1.6;">Nimm jetzt teil, sammle XP und sichere dir einen Platz im Ranking! Je schneller und genauer du antwortest, desto besser deine Platzierung.</p>
                </div>

                <!-- CTA -->
                <div style="text-align:center;margin-top:20px;">
                    <a href="https://{{ config('domains.app') }}/lernsessions" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">Jetzt teilnehmen</a>
                </div>

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
