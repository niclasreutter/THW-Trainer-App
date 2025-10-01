<!-- THW-Trainer E-Mail Vorlage -->

<div style="background:#f8fafc;padding:32px 0;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:32px 24px;">
        <div style="color:#1a202c;font-family:sans-serif;">
            <div style="text-align:center;margin-bottom:18px;">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" style="max-width:50%;height:auto;display:block;margin:auto;" />
            </div>
            <h2 style="font-size:1.5rem;font-weight:bold;margin-bottom:18px;color:#dc2626;">❌ Account wurde gelöscht</h2>
            <p style="margin-bottom:18px;font-size:1rem;">Hallo <strong>{{ $user->name }}</strong>,</p>
            <p style="margin-bottom:18px;font-size:1rem;">dein THW-Trainer Account (erstellt am {{ $accountCreatedAt }}) wurde aufgrund fehlender E-Mail-Bestätigung automatisch gelöscht.</p>
            
            <div style="background:#fef2f2;border:2px solid #fca5a5;border-radius:8px;padding:18px;margin:18px 0;text-align:center;">
                <p style="margin:0;font-size:1rem;font-weight:bold;color:#991b1b;">
                    🗑️ Dein Account und alle damit verbundenen Daten wurden permanent entfernt.
                </p>
            </div>

            <p style="margin-bottom:18px;font-size:1rem;">Das passiert, wenn ein Account länger als 9 Tage ohne E-Mail-Bestätigung existiert.</p>

            <h3 style="color:#003399;font-size:1.2rem;margin-bottom:12px;">Möchtest du trotzdem bei THW-Trainer.de lernen?</h3>
            <p style="margin-bottom:18px;font-size:1rem;">Kein Problem! Du kannst jederzeit einen neuen Account erstellen:</p>
            
            <div style="text-align:center; margin:32px 0;">
                <a href="{{ url('/register') }}" style="background:#FFD700;color:#003399;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
                    🆕 Neuen Account erstellen
                </a>
            </div>

            <div style="background:#f3f4f6;border-radius:8px;padding:15px;margin:18px 0;">
                <p style="margin:0;font-weight:bold;color:#003399;">Warum wurde der Account gelöscht?</p>
                <ul style="margin:5px 0 0 0;padding-left:20px;color:#003399;font-size:0.9rem;">
                    <li>📧 E-Mail-Adresse wurde nicht bestätigt</li>
                    <li>⏰ Account war länger als 9 Tage inaktiv</li>
                    <li>⚠️ Warnung wurde 2 Tage vorher gesendet</li>
                </ul>
            </div>

            <h3 style="color:#003399;font-size:1.2rem;margin-bottom:12px;">Beim nächsten Mal:</h3>
            <ul style="margin-bottom:18px;padding-left:20px;color:#003399;">
                <li>✅ Bestätige deine E-Mail-Adresse direkt nach der Registrierung</li>
                <li>📧 Prüfe deinen Spam-Ordner, falls keine E-Mail ankommt</li>
                <li>🔄 Fordere eine neue Bestätigungs-E-Mail an, falls nötig</li>
            </ul>

            <p style="margin-bottom:0.5rem;color:#555;"><strong>Hinweis:</strong> Alle deine Lernfortschritte und Einstellungen sind mit dem Account gelöscht worden. Ein neuer Account startet mit leeren Statistiken.</p>
            <p style="font-size:0.9rem;color:#888;margin-top:24px;text-align:center;">Viele Grüße,<br>Dein THW-Trainer Team</p>
        </div>
    </div>
</div>
