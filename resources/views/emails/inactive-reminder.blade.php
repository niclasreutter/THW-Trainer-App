<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Du fehlst uns - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="https://{{ config('domains.landing') }}/logo-thwtrainer_w.png" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>

            <!-- Body -->
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Title -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Wir vermissen dich</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Du fehlst uns!</div>

                <!-- Greeting -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $user->name }}</strong>, wir haben bemerkt, dass du seit <strong style="color:#0f172a;">{{ $daysInactive }} Tagen</strong> nicht mehr bei uns warst. Dein Wissen und dein Fortschritt warten auf dich!
                </p>

                @if($remainingQuestions > 0)

                <!-- Progress Stat Pill -->
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;border:1px solid rgba(0,51,127,0.12);margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:4px;">Dein Fortschritt</div>
                    <div style="font-size:14px;font-weight:800;color:#00337F;margin-bottom:10px;">{{ $masteredQuestions }}/{{ $totalQuestions }} Fragen gemeistert</div>
                    <!-- Progress Bar -->
                    <div style="background:#dbeafe;border-radius:6px;height:6px;overflow:hidden;">
                        <div style="background:linear-gradient(90deg,#00337F,#3b82f6);height:6px;width:{{ $progressPercentage }}%;border-radius:6px;"></div>
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:6px;">{{ $progressPercentage }}% abgeschlossen</div>
                </div>

                <!-- Stats: two stat pills side by side via table -->
                <table style="width:100%;border-collapse:separate;border-spacing:12px 0;margin-bottom:16px;">
                    <tr>
                        <td style="width:50%;background:#f0f4ff;border-radius:2rem;padding:14px 18px;border:1px solid rgba(0,51,127,0.12);vertical-align:top;">
                            <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:4px;">Gemeistert</div>
                            <div style="font-size:14px;font-weight:800;color:#00337F;">{{ $masteredQuestions }}</div>
                        </td>
                        <td style="width:50%;background:#f0f4ff;border-radius:2rem;padding:14px 18px;border:1px solid rgba(0,51,127,0.12);vertical-align:top;">
                            <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:4px;">Noch offen</div>
                            <div style="font-size:14px;font-weight:800;color:#00337F;">{{ $remainingQuestions }}</div>
                        </td>
                    </tr>
                </table>

                <!-- Motivation Info Box (conditional) -->
                @if($remainingQuestions <= 10)
                <div style="background:#f0fdf4;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #22c55e;">
                    <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:4px;">Fast geschafft!</div>
                    <p style="margin:0;font-size:13px;color:#475569;line-height:1.6;">Nur noch {{ $remainingQuestions }} Frage{{ $remainingQuestions == 1 ? '' : 'n' }}! Das schaffst du locker in ein paar Minuten.</p>
                </div>
                @elseif($remainingQuestions <= 50)
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:4px;">Du bist so nah dran!</div>
                    <p style="margin:0;font-size:13px;color:#475569;line-height:1.6;">Mit nur 10 Fragen pro Tag bist du in wenigen Tagen durch! Du packst das!</p>
                </div>
                @else
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:4px;">Schritt für Schritt</div>
                    <p style="margin:0;font-size:13px;color:#475569;line-height:1.6;">Jede Frage bringt dich weiter. Fang einfach an – der Rest folgt von selbst!</p>
                </div>
                @endif

                @else

                <!-- 100% complete -->
                <div style="background:#f0fdf4;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #22c55e;">
                    <div style="font-size:12px;font-weight:700;color:#166834;margin-bottom:4px;">Alle Fragen gemeistert!</div>
                    <p style="margin:0;font-size:13px;color:#475569;line-height:1.6;">Du hast bereits alle <strong>{{ $totalQuestions }} Fragen</strong> gemeistert!</p>
                    <!-- Progress Bar 100% -->
                    <div style="background:#dbeafe;border-radius:6px;height:6px;overflow:hidden;margin-top:10px;">
                        <div style="background:linear-gradient(90deg,#00337F,#3b82f6);height:6px;width:100%;border-radius:6px;"></div>
                    </div>
                </div>

                <div style="background:#f0fdf4;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #22c55e;">
                    <div style="font-size:12px;font-weight:700;color:#166834;margin-bottom:4px;">Bleib dran!</div>
                    <p style="margin:0;font-size:13px;color:#475569;line-height:1.6;">Wiederhole deine Fragen regelmäßig, um dein Wissen frisch zu halten. Übung macht den Meister!</p>
                </div>

                @endif

                @if($user->points > 0)
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Du hast bereits <strong style="color:#0f172a;">{{ $user->points }} Punkte</strong> gesammelt und bist auf Level <strong style="color:#0f172a;">{{ $user->level }}</strong>. Lass uns zusammen weiter an deinem Erfolg arbeiten!
                </p>
                @endif

                <!-- CTA Button -->
                <div style="text-align:center;margin:24px 0 8px 0;">
                    <a href="https://{{ config('domains.app') }}/practice-menu" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">
                        @if($remainingQuestions > 0)
                            Jetzt weiterlernen
                        @else
                            Wissen auffrischen
                        @endif
                    </a>
                </div>

                <p style="margin:16px 0 0 0;font-size:13px;color:#475569;line-height:1.6;text-align:center;">
                    @if($remainingQuestions > 0)
                        Komm zurück und beende, was du begonnen hast. Wir glauben an dich!
                    @else
                        Bleib am Ball und halte dein Wissen frisch. Du bist großartig!
                    @endif
                </p>

            </div>

            <!-- Footer -->
            <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;">
                <div style="text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:11px;color:#94a3b8;line-height:1.6;"><strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung</p>
                    <p style="margin:0 0 4px 0;font-size:11px;color:#94a3b8;">Diese E-Mail wurde automatisch gesendet, weil du {{ $daysInactive }} Tage inaktiv warst und E-Mail-Benachrichtigungen aktiviert hast.</p>
                    <p style="margin:0 0 6px 0;font-size:11px;color:#94a3b8;"><a href="https://{{ config('domains.landing') }}/impressum" style="color:#94a3b8;text-decoration:none;">Impressum</a> &middot; <a href="https://{{ config('domains.landing') }}/datenschutz" style="color:#94a3b8;text-decoration:none;">Datenschutz</a></p>
                    <p style="margin:0;font-size:11px;color:#cbd5e1;"><a href="https://{{ config('domains.app') }}/profile" style="color:#64748b;text-decoration:none;">E-Mail-Einstellungen ändern</a></p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
