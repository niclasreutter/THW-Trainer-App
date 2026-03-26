<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak Freeze - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('logo-thwtrainer_w.png') . '?v=' . filemtime(public_path('logo-thwtrainer_w.png')) }}" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>

            <!-- Body -->
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Title -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Streak Freeze</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Dein Streak wurde geschützt!</div>

                <!-- Greeting -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $user->name }}</strong>,
                </p>

                <!-- Freeze Info Box -->
                <div style="background:#f0f4ff;border-left:3px solid #3b82f6;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:4px;">Dein Streak wurde automatisch geschützt!</div>
                    <div style="font-size:12px;color:#1e3a5f;line-height:1.5;">Du hast gestern nicht gelernt, aber dein <strong>Streak Freeze</strong> hat deinen <strong>{{ $streakDays }}-Tage Streak</strong> gerettet.</div>
                </div>

                <!-- Remaining Freezes -->
                @if($freezesRemaining > 0)
                <div style="background:#f0fdf4;border-left:3px solid #22c55e;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:4px;">{{ $freezesRemaining }} Freeze{{ $freezesRemaining > 1 ? 's' : '' }} verbleibend</div>
                    <div style="font-size:12px;color:#166534;line-height:1.5;">Du hast diese Woche noch {{ $freezesRemaining }} Streak Freeze{{ $freezesRemaining > 1 ? 's' : '' }} übrig. Versuche trotzdem, täglich zu lernen!</div>
                </div>
                @else
                <div style="background:#fef2f2;border-left:3px solid #ef4444;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#991b1b;margin-bottom:4px;">Keine Freezes mehr übrig!</div>
                    <div style="font-size:12px;color:#991b1b;line-height:1.5;">Du hast diese Woche keine Streak Freezes mehr. Wenn du morgen nicht lernst, geht dein Streak verloren! Im Shop kannst du neue Freezes kaufen.</div>
                </div>
                @endif

                <!-- Encouragement -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Lerne heute weiter, um deinen <strong style="color:#0f172a;">{{ $streakDays }}-Tage Streak</strong> auszubauen. Beantworte mindestens 20 Fragen oder absolviere eine Prüfung.
                </p>

                <!-- CTA Button -->
                <div style="text-align:center;margin:24px 0 8px;">
                    <a href="https://{{ config('domains.app') }}/practice-menu" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">Jetzt weiterlernen</a>
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
