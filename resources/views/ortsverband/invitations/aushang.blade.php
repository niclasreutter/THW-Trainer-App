@php
    $host = parse_url($registerUrl, PHP_URL_HOST) ?: 'thw-trainer.de';
    $host = preg_replace('/^www\./', '', $host);
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aushang {{ $ortsverband->name }} - THW-Trainer</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@700;800&family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            background: #e5e7eb;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---- Bildschirm-Toolbar (nicht im Druck) ---- */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(8px);
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .toolbar__hint {
            color: #cbd5e1;
            font-size: 0.8rem;
            margin-right: auto;
            padding-left: 0.25rem;
        }
        .tb-btn {
            font: inherit;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            border-radius: 0.6rem;
            padding: 0.6rem 1.15rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: transform 0.12s ease;
        }
        .tb-btn:hover { transform: translateY(-1px); }
        .tb-btn--primary { background: #00337F; color: #fff; }
        .tb-btn--ghost { background: rgba(255, 255, 255, 0.12); color: #e2e8f0; }

        /* ---- A4 Blatt ---- */
        .sheet {
            width: 210mm;
            height: 297mm;
            margin: 24px auto;
            background: #ffffff;
            color: #1e293b;
            font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(0, 51, 127, 0.10);
        }

        @page { size: A4; margin: 0; }
        @media print {
            html, body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <span class="toolbar__hint">Tipp: Im Druckdialog „Als PDF speichern&#8220; wählen &middot; Ränder: Keine</span>
        <button type="button" class="tb-btn tb-btn--primary" onclick="window.print()">Als PDF speichern / Drucken</button>
        <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="tb-btn tb-btn--ghost">Zurück</a>
    </div>

    <div class="sheet">

        <div style="height: 8px; background: #00337F; flex: 0 0 auto;"></div>

        <div style="flex: 1; display: flex; flex-direction: column; padding: 44px 56px 40px 56px; min-height: 0;">

            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="THW-Trainer Logo" style="width: 46px; height: 46px; display: block;" />
                    @endif
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <div style="font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 27px; line-height: 1; color: #00337F; letter-spacing: 0.005em;">THW-Trainer</div>
                        <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 500; font-size: 10px; letter-spacing: 0.14em; color: #64748b;">LERN- &amp; PRÜFUNGSPLATTFORM</div>
                    </div>
                </div>
                <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 11px; letter-spacing: 0.10em; color: #00337F; background: rgba(0,51,127,0.06); border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 8px 16px;">SCANNEN &amp; MITMACHEN</div>
            </div>

            <div style="margin-top: 46px;">
                <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 12px; letter-spacing: 0.12em; color: #64748b; border-left: 3px solid #d97706; padding-left: 12px;">FÜR ALLE IN DER GRUNDAUSBILDUNG</div>
                <div style="font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 64px; line-height: 1.02; letter-spacing: -0.005em; color: #00337F; margin-top: 16px;">Übe die Theorie der<br />Grundausbildung.</div>
                <p style="font-size: 17px; line-height: 1.5; color: #475569; max-width: 540px; margin: 18px 0 0 0;">Theoriefragen üben, Prüfungen simulieren und deinen Fortschritt verfolgen, kostenlos und direkt auf dem Handy.</p>
            </div>

            <div style="display: flex; gap: 36px; margin-top: 42px; align-items: stretch;">

                <div style="flex: 0 0 320px; background: #ffffff; border: 1px solid rgba(0,51,127,0.15); border-radius: 24px 8px 24px 8px; box-shadow: 0 4px 16px rgba(0,51,127,0.08); padding: 24px 24px 22px 24px; display: flex; flex-direction: column; align-items: center; gap: 16px;">
                    <img src="{{ $qrDataUri }}" alt="QR-Code zur Registrierung für {{ $ortsverband->name }}" style="width: 256px; height: 256px; display: block;" />
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 7px; text-align: center;">
                        <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 500; font-size: 10px; letter-spacing: 0.14em; color: #64748b;">DEIN ORTSVERBAND</div>
                        <div style="font-weight: 700; font-size: 18px; line-height: 1.2; color: #1e293b;">{{ $ortsverband->name }}</div>
                        <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 14px; letter-spacing: 0.06em; color: #00337F; background: rgba(0,51,127,0.06); border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 5px 14px;">{{ $invitation->code }}</div>
                    </div>
                </div>

                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; padding: 4px 0;">
                    <div style="display: flex; flex-direction: column; gap: 22px;">
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="flex: 0 0 auto; width: 42px; height: 42px; background: #00337F; color: #ffffff; border-radius: 12px 4px 12px 4px; display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 21px;">1</div>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div style="font-weight: 700; font-size: 17px; color: #1e293b;">Scannen</div>
                                <div style="font-size: 14px; line-height: 1.45; color: #64748b;">QR-Code mit der Handy-Kamera aufnehmen.</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="flex: 0 0 auto; width: 42px; height: 42px; background: #00337F; color: #ffffff; border-radius: 12px 4px 12px 4px; display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 21px;">2</div>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div style="font-weight: 700; font-size: 17px; color: #1e293b;">Registrieren</div>
                                <div style="font-size: 14px; line-height: 1.45; color: #64748b;">Kostenlos anmelden, du trittst deinem Ortsverband automatisch bei.</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="flex: 0 0 auto; width: 42px; height: 42px; background: #00337F; color: #ffffff; border-radius: 12px 4px 12px 4px; display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 21px;">3</div>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div style="font-weight: 700; font-size: 17px; color: #1e293b;">Loslegen</div>
                                <div style="font-size: 14px; line-height: 1.45; color: #64748b;">Sofort für die Grundausbildung lernen.</div>
                            </div>
                        </div>
                    </div>

                    <div style="background: rgba(0,51,127,0.04); border: 1px solid rgba(0,51,127,0.10); border-radius: 4px 12px 4px 12px; padding: 14px 16px; font-size: 13px; line-height: 1.5; color: #475569; margin-top: 22px;">
                        <span style="font-weight: 700; color: #1e293b;">Registrierung ohne Scannen:</span> Auf <span style="font-weight: 700; color: #00337F;">{{ $host }}</span> registrieren und den Code <span style="font-family: 'IBM Plex Mono', monospace; font-weight: 600; color: #00337F;">{{ $invitation->code }}</span> eingeben.
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 8px; flex-wrap: nowrap; margin-top: 36px;">
                <div style="font-weight: 600; font-size: 12px; color: #1e293b; border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 7px 13px; white-space: nowrap;">Alle offiziellen + optionale Fragen</div>
                <div style="font-weight: 600; font-size: 12px; color: #1e293b; border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 7px 13px; white-space: nowrap;">Prüfungssimulation</div>
                <div style="font-weight: 600; font-size: 12px; color: #1e293b; border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 7px 13px; white-space: nowrap;">Fortschritt &amp; Statistiken</div>
                <div style="font-weight: 600; font-size: 12px; color: #1e293b; border: 1px solid rgba(0,51,127,0.15); border-radius: 9999px; padding: 7px 13px; white-space: nowrap;">100&nbsp;% kostenlos</div>
            </div>

            <div style="margin-top: auto; border-top: 1px solid rgba(0,51,127,0.10); padding-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                <div style="font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 12px; letter-spacing: 0.04em; color: #b45309;">{{ $host }}</div>
                <div style="font-size: 11.5px; color: #64748b; text-align: right;">Ein Lernangebot für Helferinnen und Helfer des THW · Aufgehängt vom {{ $ortsverband->name }}</div>
            </div>
        </div>
    </div>
</body>
</html>
