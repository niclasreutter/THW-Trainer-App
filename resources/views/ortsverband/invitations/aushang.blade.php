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
    <link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --thw-blue: #00337F;
            --thw-blue-dark: #002255;
            --gold-start: #fbbf24;
            --gold-end: #f59e0b;
            --ink: #0f172a;
            --muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            background: #e2e8f0;
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---- Print toolbar (nicht im Druck) ---- */
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
            transition: transform 0.12s ease, filter 0.12s ease;
        }
        .tb-btn:hover { transform: translateY(-1px); }
        .tb-btn--primary {
            background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
            color: #3a2a00;
        }
        .tb-btn--ghost {
            background: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        /* ---- A4 Blatt ---- */
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 1.5rem auto;
            background: #fff;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Kopf-Band THW-Blau */
        .sheet__head {
            background: linear-gradient(135deg, var(--thw-blue), var(--thw-blue-dark));
            color: #fff;
            padding: 14mm 16mm 12mm;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        .sheet__head::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold-start), var(--gold-end));
        }
        .brand-logo {
            height: 42px;
            width: auto;
            background: #fff;
            border-radius: 10px;
            padding: 5px 8px;
        }
        .brand-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.9rem;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .brand-tag {
            margin-left: auto;
            text-align: right;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 600;
        }

        /* Body */
        .sheet__body {
            flex: 1;
            padding: 12mm 16mm 6mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .kicker {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--gold-end);
            margin-bottom: 10px;
        }
        .headline {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 0.98;
            color: var(--thw-blue);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .headline span {
            display: block;
            background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: var(--gold-end);
        }
        .subline {
            margin-top: 10px;
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 135mm;
            line-height: 1.5;
        }

        /* QR Karte */
        .qr-card {
            margin: 9mm 0 6mm;
            background: #fff;
            border: 3px solid var(--thw-blue);
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 10px 30px rgba(0, 51, 127, 0.14);
            position: relative;
        }
        .qr-card::before {
            content: "SCANNEN & MITMACHEN";
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
            color: #3a2a00;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            font-size: 0.62rem;
            letter-spacing: 0.14em;
            padding: 4px 12px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .qr-card img {
            display: block;
            width: 62mm;
            height: 62mm;
        }
        .ov-line {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--ink);
        }
        .ov-line b { color: var(--thw-blue); }
        .code-line {
            margin-top: 4px;
            font-size: 0.85rem;
            color: var(--muted);
        }
        .code-line code {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--ink);
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px 8px;
            letter-spacing: 0.04em;
        }

        /* Schritte */
        .steps {
            display: flex;
            gap: 10px;
            margin-top: 9mm;
            width: 100%;
            max-width: 165mm;
        }
        .step {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 10px;
            background: #f8fafc;
        }
        .step__num {
            width: 30px;
            height: 30px;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--thw-blue), var(--thw-blue-dark));
            color: #fff;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
        }
        .step__title {
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--ink);
            margin-bottom: 2px;
        }
        .step__text {
            font-size: 0.76rem;
            color: var(--muted);
            line-height: 1.35;
        }

        /* Benefits */
        .benefits {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-top: 8mm;
        }
        .benefit {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--thw-blue);
            background: rgba(0, 51, 127, 0.06);
            border: 1px solid rgba(0, 51, 127, 0.12);
            border-radius: 999px;
            padding: 5px 12px;
        }

        /* Fuß-Band */
        .sheet__foot {
            margin-top: auto;
            background: var(--thw-blue-dark);
            color: #fff;
            padding: 8mm 16mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .foot-url {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }
        .foot-url span {
            background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .foot-note {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.72);
            text-align: right;
            line-height: 1.4;
        }

        /* ---- Druck ---- */
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                min-height: 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <span class="toolbar__hint">Tipp: Im Druckdialog „Als PDF speichern&#8220; wählen &middot; Ränder: Keine</span>
        <button type="button" class="tb-btn tb-btn--primary" onclick="window.print()">Als PDF speichern / Drucken</button>
        <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="tb-btn tb-btn--ghost">Zurück</a>
    </div>

    <div class="sheet">
        <div class="sheet__head">
            @if($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="THW-Trainer" class="brand-logo">
            @endif
            <span class="brand-name">THW-Trainer</span>
            <span class="brand-tag">Lern- &amp;<br>Prüfungsplattform</span>
        </div>

        <div class="sheet__body">
            <div class="kicker">Für alle in der Grundausbildung</div>
            <h1 class="headline">
                Übe für deine
                <span>THW-Prüfung</span>
            </h1>
            <p class="subline">
                Theoriefragen üben, Prüfungen simulieren und deinen Fortschritt verfolgen &ndash;
                kostenlos und direkt auf dem Handy. Scanne den Code und leg los.
            </p>

            <div class="qr-card">
                <img src="{{ $qrDataUri }}" alt="QR-Code zur Registrierung für {{ $ortsverband->name }}">
            </div>

            <div class="ov-line">Ortsverband <b>{{ $ortsverband->name }}</b></div>
            <div class="code-line">Kein Scanner? Auf {{ $host }} registrieren, Code: <code>{{ $invitation->code }}</code></div>

            <div class="steps">
                <div class="step">
                    <div class="step__num">1</div>
                    <div class="step__title">Scannen</div>
                    <div class="step__text">QR-Code mit der Handy-Kamera aufnehmen.</div>
                </div>
                <div class="step">
                    <div class="step__num">2</div>
                    <div class="step__title">Registrieren</div>
                    <div class="step__text">Kostenlos anmelden &ndash; du trittst dem OV automatisch bei.</div>
                </div>
                <div class="step">
                    <div class="step__num">3</div>
                    <div class="step__title">Loslegen</div>
                    <div class="step__text">Sofort für die Grundausbildung lernen.</div>
                </div>
            </div>

            <div class="benefits">
                <span class="benefit">Theoriefragen-Training</span>
                <span class="benefit">Prüfungssimulation</span>
                <span class="benefit">Fortschritt &amp; Statistiken</span>
                <span class="benefit">100 % kostenlos</span>
            </div>
        </div>

        <div class="sheet__foot">
            <div class="foot-url"><span>{{ $host }}</span></div>
            <div class="foot-note">
                Ein Angebot für das Technische Hilfswerk.<br>
                Aufgehängt vom OV {{ $ortsverband->name }}.
            </div>
        </div>
    </div>
</body>
</html>
