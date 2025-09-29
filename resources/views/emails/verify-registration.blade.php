<!-- THW-Trainer E-Mail Vorlage -->


<div style="background:#f8fafc;padding:32px 0;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:32px 24px;">
        <div style="color:#1a202c;font-family:sans-serif;">
            <div style="text-align:center;margin-bottom:18px;">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" style="max-width:50%;height:auto;display:block;margin:auto;" />
            </div>
            <h2 style="font-size:1.5rem;font-weight:bold;margin-bottom:18px;color:#003399;">Willkommen beim THW-Trainer!</h2>
        <div style="color:#1a202c;font-family:sans-serif;">
            <p style="margin-bottom:18px;font-size:1rem;">Vielen Dank für deine Registrierung.<br>Um alle Funktionen zu nutzen, bestätige bitte deine E-Mail-Adresse.</p>
            <ul style="margin-bottom:18px;padding-left:20px;color:#003399;">
                <li>Dein Account wird nach Bestätigung freigeschaltet</li>
                <li>Du kannst deinen Lernfortschritt speichern und Prüfungen simulieren</li>
            </ul>
            <div style="text-align:center; margin:32px 0;">
                <a href="{{ $verificationUrl }}" style="background:#FFD700;color:#003399;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
                    E-Mail bestätigen
                </a>
            </div>
            <p style="margin-bottom:0.5rem;color:#555;">Falls du dich nicht registriert hast, kannst du diese E-Mail ignorieren.</p>
            <p style="font-size:0.9rem;color:#888;margin-top:24px;text-align:center;">Viele Grüße,<br>Dein THW-Trainer Team</p>
        </div>
    </div>
</div>
