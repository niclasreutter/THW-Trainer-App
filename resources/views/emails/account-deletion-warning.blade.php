<!-- THW-Trainer E-Mail Vorlage -->

<div style="background:#f8fafc;padding:32px 0;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:32px 24px;">
        <div style="color:#1a202c;font-family:sans-serif;">
            <div style="text-align:center;margin-bottom:18px;">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" style="max-width:50%;height:auto;display:block;margin:auto;" />
            </div>
            <h2 style="font-size:1.5rem;font-weight:bold;margin-bottom:18px;color:#dc2626;">⚠️ Account-Löschung in 2 Tagen!</h2>
            <p style="margin-bottom:18px;font-size:1rem;">Hallo <strong>{{ $user->name }}</strong>,</p>
            <p style="margin-bottom:18px;font-size:1rem;">dein THW-Trainer Account wurde am <strong>{{ $accountCreatedAt }}</strong> erstellt, aber deine E-Mail-Adresse wurde noch nicht bestätigt.</p>
            
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:18px 0;text-align:center;">
                <p style="margin:0;font-size:1rem;font-weight:bold;color:#92400e;">
                    ⏰ Dein Account wird in <strong>2 Tagen automatisch gelöscht</strong>, 
                    wenn du deine E-Mail-Adresse nicht bestätigst!
                </p>
            </div>

            <p style="margin-bottom:18px;font-size:1rem;">Um deinen Account zu behalten und mit dem Lernen zu beginnen, klicke einfach auf den Button unten:</p>
            
            <div style="text-align:center; margin:32px 0;">
                <a href="{{ $verificationUrl }}" style="background:#FFD700;color:#003399;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
                    📧 E-Mail-Adresse jetzt bestätigen
                </a>
            </div>

            <div style="background:#f3f4f6;border-radius:8px;padding:15px;margin:18px 0;">
                <p style="margin:0;font-weight:bold;color:#003399;">Account-Details:</p>
                <p style="margin:5px 0 0 0;font-size:0.9rem;">📧 E-Mail: {{ $user->email }}</p>
                <p style="margin:5px 0 0 0;font-size:0.9rem;">📅 Erstellt: {{ $accountCreatedAt }}</p>
            </div>

            <p style="margin-bottom:12px;font-size:1rem;">Nach der Bestätigung kannst du:</p>
            <ul style="margin-bottom:18px;padding-left:20px;color:#003399;">
                <li>🏆 An THW-Prüfungsfragen üben</li>
                <li>📊 Deinen Lernfortschritt verfolgen</li>
                <li>🎯 Prüfungs-Simulationen absolvieren</li>
                <li>⭐ Fragen als Favoriten markieren</li>
            </ul>

            <p style="margin-bottom:0.5rem;color:#555;">Falls du den Account nicht benötigst, musst du nichts tun. Er wird automatisch gelöscht.</p>
            <p style="font-size:0.9rem;color:#888;margin-top:24px;text-align:center;">Viele Grüße,<br>Dein THW-Trainer Team</p>
        </div>
    </div>
</div>
