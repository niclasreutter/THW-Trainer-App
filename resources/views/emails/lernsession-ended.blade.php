<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernsession beendet - THW Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">
            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <img src="https://thw-trainer.de/logo-thwtrainer_w.png" alt="THW" style="width:18px;height:18px;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</span>
                </div>
            </div>
            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                <!-- Label + Title -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Lernsession beendet</div>
                @if($isWinner)
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Herzlichen Glückwunsch!</div>
                @else
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">{{ $session->title }} &ndash; Ergebnisse</div>
                @endif

                <!-- Greeting -->
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $user->name }}</strong>,<br>
                    @if($isWinner)
                    du hast die Lernsession <strong style="color:#0f172a;">{{ $session->title }}</strong> gewonnen!
                    @else
                    die Lernsession <strong style="color:#0f172a;">{{ $session->title }}</strong> ist beendet. Hier sind deine Ergebnisse:
                    @endif
                </p>

                <!-- Placement Stat Pill (large) -->
                <div style="background:#f0f4ff;border-radius:2rem;padding:20px 18px;margin-bottom:16px;border:1px solid rgba(0,51,127,0.12);text-align:center;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:8px;">Deine Platzierung</div>
                    <div style="font-size:40px;font-weight:800;color:#00337F;line-height:1;">Platz {{ $participant->final_rank }}</div>
                    <div style="font-size:13px;font-weight:600;color:#64748b;margin-top:6px;">von {{ $totalParticipants }} Teilnehmer{{ $totalParticipants != 1 ? 'n' : '' }}</div>
                </div>

                <!-- Stats Table as pill rows -->
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Deine Statistiken</div>

                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:8px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Fragen beantwortet</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $participant->questions_answered }}</span>
                    </div>
                </div>
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:8px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Richtige Antworten</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $participant->questions_correct }}</span>
                    </div>
                </div>
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:8px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Genauigkeit</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ number_format($participant->accuracy_percent, 1) }}%</span>
                    </div>
                </div>
                <div style="background:#f0f4ff;border-radius:2rem;padding:14px 18px;margin-bottom:16px;border:1px solid rgba(0,51,127,0.12);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Verdiente XP</span>
                        <span style="font-size:14px;font-weight:800;color:#00337F;">{{ $participant->xp_earned }} XP</span>
                    </div>
                </div>

                @if($isWinner && $hasLootbox)
                <!-- Gold Lootbox Success Box -->
                <div style="background:#f0fdf4;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #22c55e;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#166534;margin-bottom:4px;">Gold Lootbox erhalten</div>
                    <p style="margin:0;font-size:13px;color:#166834;line-height:1.6;">Als Gewinner der Lernsession hast du eine Gold-Belohnungskiste erhalten. Öffne sie im Dashboard, um deine Belohnung zu entdecken!</p>
                </div>
                @endif

                <!-- Motivation -->
                <p style="margin:0 0 20px 0;font-size:13px;color:#475569;line-height:1.6;">
                    @if($isWinner)
                    Großartige Leistung! Halte dein Niveau und nimm an weiteren Lernsessions teil.
                    @elseif($participant->final_rank <= 3)
                    Tolle Leistung! Du warst ganz nah dran am Sieg. Beim nächsten Mal schaffst du es!
                    @else
                    Gut gemacht! Übung macht den Meister &ndash; nimm an weiteren Lernsessions teil, um dich zu verbessern.
                    @endif
                </p>

                <!-- CTA -->
                <div style="text-align:center;margin-top:4px;">
                    <a href="https://thw-trainer.de/lernsessions" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">Weitere Lernsessions anzeigen</a>
                </div>

            </div>
            <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;">
                <div style="text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:11px;color:#94a3b8;line-height:1.6;"><strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung</p>
                    <p style="margin:0 0 6px 0;font-size:11px;color:#94a3b8;"><a href="https://thw-trainer.de/impressum" style="color:#94a3b8;text-decoration:none;">Impressum</a> &middot; <a href="https://thw-trainer.de/datenschutz" style="color:#94a3b8;text-decoration:none;">Datenschutz</a></p>
                    <p style="margin:0;font-size:11px;color:#cbd5e1;"><a href="https://thw-trainer.de/profile" style="color:#64748b;text-decoration:none;">E-Mail-Einstellungen ändern</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
