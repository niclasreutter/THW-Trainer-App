<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak-Alarm - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>

            <!-- Body -->
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Title -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Streak-Alarm</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Dein Streak ist in Gefahr!</div>

                <!-- Greeting -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $user->name }}</strong>,
                </p>

                @php
                    // daily_questions_solved nur verwenden wenn es von heute ist
                    $isToday = $user->daily_questions_date && \Carbon\Carbon::parse($user->daily_questions_date)->isToday();
                    $solved = $isToday ? ($user->daily_questions_solved ?? 0) : 0;
                    $goal = $user->daily_streak_goal ?? 20;
                    $remaining = max(0, $goal - $solved);
                @endphp

                <!-- Progress Stat Pill -->
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:8px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:#64748b;">Fortschritt heute</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $solved }}/{{ $goal }} Fragen</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div style="background:#dbeafe;border-radius:4px;height:6px;overflow:hidden;margin-bottom:16px;">
                    <div style="background:linear-gradient(90deg,#00337F,#3b82f6);height:6px;width:{{ $goal > 0 ? min(100, ($solved / $goal) * 100) : 100 }}%;border-radius:4px;"></div>
                </div>

                <!-- Motivation -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Du warst schon <strong style="color:#0f172a;">{{ $streakDays }} Tage</strong> in Folge aktiv - das ist fantastisch! Lass uns diesen Streak nicht unterbrechen!
                </p>

                @if($streakDays >= 3)
                <!-- Streak-Bonus aktiv -->
                <div style="background:#f0f4ff;border-left:3px solid #3b82f6;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:4px;">Streak-Bonus aktiv!</div>
                    <div style="font-size:12px;color:#1e3a5f;line-height:1.5;">Da du bereits {{ $streakDays }} Tage Streak hast, bekommst du <strong>doppelte Punkte</strong> für jede richtige Antwort! Das sind 20 Punkte statt 10 Punkte pro Frage - oder willst du wieder nur 10 Punkte bekommen?</div>
                </div>
                @else
                <!-- Noch kein Bonus -->
                <div style="background:#fffbeb;border-left:3px solid #f59e0b;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:4px;">Noch {{ 3 - $streakDays }} Tag{{ 3 - $streakDays == 1 ? '' : 'e' }} bis zum Streak-Bonus!</div>
                    <div style="font-size:12px;color:#92400e;line-height:1.5;">Lerne heute und morgen weiter, dann bekommst du ab dem {{ $streakDays + 1 }}. Tag <strong>doppelte Punkte</strong> für jede richtige Antwort! Das sind 20 Punkte statt 10 Punkte pro Frage - willst du dir das entgehen lassen?</div>
                </div>
                @endif

                <!-- Remaining Info -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    @if($remaining > 0)
                        Beantworte heute noch <strong style="color:#0f172a;">{{ $remaining }} Frage{{ $remaining == 1 ? '' : 'n' }}</strong> oder absolviere eine Prüfung, um deinen Streak zu retten.
                    @else
                        Du hast heute schon 20 Fragen beantwortet - dein Streak ist gesichert!
                    @endif
                </p>

                <!-- CTA Button -->
                <div style="text-align:center;margin:24px 0 8px;">
                    <a href="https://{{ config('domains.app') }}/practice-menu" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">Jetzt lernen und Streak retten</a>
                </div>

                <!-- Encouragement -->
                <p style="margin:16px 0 0 0;font-size:13px;color:#475569;line-height:1.6;text-align:center;">
                    Du schaffst das! {{ $remaining }} Frage{{ $remaining == 1 ? '' : 'n' }} dauern nur wenige Minuten und dein Streak bleibt erhalten.
                </p>

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
