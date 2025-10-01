<!-- THW-Trainer E-Mail Vorlage -->

<div style="background:#f8fafc;padding:32px 0;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:32px 24px;">
        <div style="color:#1a202c;font-family:sans-serif;">
            <div style="text-align:center;margin-bottom:18px;">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" style="max-width:50%;height:auto;display:block;margin:auto;" />
            </div>
            <h2 style="font-size:1.5rem;font-weight:bold;margin-bottom:18px;color:#003399;">E-Mail-Adresse bestätigen</h2>
            <p style="margin-bottom:18px;font-size:1rem;">Hallo <strong>{{ $user->name }}</strong>,</p>
            <p style="margin-bottom:18px;font-size:1rem;">du hast deine E-Mail-Adresse in deinem THW-Trainer Profil geändert. Um die Änderung zu bestätigen, klicke bitte auf den Button unten.</p>
            
            <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:8px;padding:18px;margin:18px 0;text-align:center;">
                <p style="margin:0;font-size:1rem;font-weight:bold;color:#92400e;">
                    ⏰ <strong>Wichtig:</strong> Dieser Link ist nur für 5 Minuten gültig.
                </p>
            </div>

            <div style="text-align:center; margin:32px 0;">
                <a href="{{ $verificationUrl }}" style="background:#FFD700;color:#003399;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
                    E-Mail-Adresse bestätigen
                </a>
            </div>

            <p style="margin-bottom:0.5rem;color:#555;">Falls du diese Änderung nicht vorgenommen hast, ignoriere diese E-Mail einfach.</p>
            <p style="font-size:0.8rem;color:#888;margin-top:24px;text-align:center;">Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>{{ $verificationUrl }}</p>
            <p style="font-size:0.9rem;color:#888;margin-top:12px;text-align:center;">Viele Grüße,<br>Dein THW-Trainer Team</p>
        </div>
    </div>
</div>
