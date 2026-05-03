<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datenexport - THW Trainer</title>
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

                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Datenexport</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Dein persönlicher Datenexport</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">Hallo <strong>{{ $user->name }}</strong>,</p>
                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    auf deinen Wunsch hin haben wir am <strong>{{ $requestedAt }}</strong> einen vollständigen Export deiner bei
                    THW-Trainer gespeicherten Daten erstellt. Die Daten findest du als CSV-Dateien im Anhang dieser E-Mail.
                </p>

                <!-- Hinweis-Box -->
                <div style="background:#f0f4ff;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #3b82f6;">
                    <p style="margin:0 0 8px 0;font-size:13px;font-weight:700;color:#1e3a5f;">Was ist enthalten?</p>
                    <ul style="margin:0;padding-left:18px;font-size:13px;color:#1e3a5f;line-height:1.8;">
                        <li><strong>profil.csv</strong> — Profil-Stammdaten und Consents</li>
                        <li><strong>benachrichtigungen.csv</strong> — Benachrichtigungseinstellungen</li>
                        <li><strong>ortsverbaende.csv</strong> — Ortsverband-Mitgliedschaften ({{ $summary['ortsverbaende'] }})</li>
                        <li><strong>fragen-fortschritt.csv</strong> — Lernfortschritt Grundausbildung ({{ $summary['questionProgress'] }} Einträge)</li>
                        <li><strong>lehrgang-fortschritt.csv</strong> — Lehrgang-Fortschritt ({{ $summary['lehrgangProgress'] }} Einträge)</li>
                        <li><strong>zusatzfragen-fortschritt.csv</strong> — Zusatzfragen-Fortschritt ({{ $summary['extraQuestionProgress'] }} Einträge)</li>
                        <li><strong>pruefungen.csv</strong> — Prüfungsstatistiken ({{ $summary['examStatistics'] }} Versuche)</li>
                        <li><strong>xp-historie.csv</strong> — XP-Verlauf ({{ $summary['xpHistory'] }} Buchungen)</li>
                        <li><strong>lesezeichen.csv</strong> — Markierte Fragen ({{ $summary['bookmarks'] }} Lesezeichen)</li>
                    </ul>
                </div>

                <p style="margin:0 0 12px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Die CSV-Dateien sind UTF-8 kodiert (mit BOM) und nutzen das Semikolon als Trennzeichen — sie lassen sich
                    direkt mit Excel, Numbers, LibreOffice Calc oder einem Texteditor öffnen.
                </p>

                <!-- DSGVO-Hinweis -->
                <div style="background:#f8fafc;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #00337F;">
                    <p style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#0f172a;">Recht auf Datenübertragbarkeit (Art. 20 DSGVO)</p>
                    <p style="margin:0;font-size:12px;color:#475569;line-height:1.6;">
                        Du erhältst hiermit alle personenbezogenen Daten, die du uns bereitgestellt hast oder die wir aus deiner Nutzung
                        des Dienstes gespeichert haben — in einem strukturierten, gängigen und maschinenlesbaren Format.
                        Aggregierte, anonyme Statistiken sind nicht enthalten, da sie keinen Personenbezug aufweisen.
                    </p>
                </div>

                <!-- Sicherheitshinweis -->
                <div style="background:#fef9e7;border-radius:8px;padding:14px 16px;margin-bottom:16px;border-left:3px solid #f59e0b;">
                    <p style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#78350f;">Sicherheitshinweis</p>
                    <p style="margin:0;font-size:12px;color:#78350f;line-height:1.6;">
                        Diese E-Mail enthält persönliche Daten. Bewahre sie sicher auf und leite sie nicht an Dritte weiter.
                        Falls du diesen Export nicht angefordert hast, melde dich bitte umgehend bei
                        <a href="mailto:protokolle@thw-trainer.de" style="color:#78350f;font-weight:700;">protokolle@thw-trainer.de</a>.
                    </p>
                </div>

                <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                    Hinweis: Datenexporte sind aus Gründen des Missbrauchsschutzes auf <strong>einmal pro Woche</strong> und
                    <strong>maximal zweimal pro Monat</strong> beschränkt.
                </p>

            </div>
            <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;">
                <div style="text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:11px;color:#94a3b8;line-height:1.6;"><strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung</p>
                    <p style="margin:0 0 6px 0;font-size:11px;color:#94a3b8;"><a href="https://{{ config('domains.landing') }}/impressum" style="color:#94a3b8;text-decoration:none;">Impressum</a> &middot; <a href="https://{{ config('domains.landing') }}/datenschutz" style="color:#94a3b8;text-decoration:none;">Datenschutz</a></p>
                    <p style="margin:0;font-size:11px;color:#cbd5e1;"><a href="https://{{ config('domains.app') }}/einstellungen" style="color:#64748b;text-decoration:none;">Einstellungen verwalten</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
