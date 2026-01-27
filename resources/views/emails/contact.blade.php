<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Kontaktanfrage - THW-Trainer</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background:#f8fafc;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">

            <!-- Header -->
            <div style="background:#003399;color:#FFD700;padding:24px;text-align:center;">
                <h1 style="margin:0;font-size:24px;font-weight:600;">Neue Kontaktanfrage</h1>
                <p style="margin:8px 0 0 0;font-size:14px;color:#ffffff;">THW-Trainer App</p>
            </div>

            <!-- Content -->
            <div style="padding:32px;">

                <!-- Kategorie -->
                <div style="margin-bottom:20px;">
                    <p style="margin:0 0 8px 0;font-size:14px;font-weight:600;color:#003399;">Kategorie:</p>
                    <div style="background:#f9fafb;padding:12px;border-left:3px solid #FFD700;">
                        @php
                            $badgeColor = match($contactMessage->type) {
                                'feedback' => 'background:#dbeafe;color:#1e40af;',
                                'feature' => 'background:#fef3c7;color:#92400e;',
                                'bug' => 'background:#fee2e2;color:#991b1b;',
                                default => 'background:#e5e7eb;color:#374151;',
                            };
                        @endphp
                        <span style="display:inline-block;padding:5px 15px;border-radius:20px;font-weight:bold;font-size:14px;{{ $badgeColor }}">
                            {{ $contactMessage->type_label }}
                        </span>
                    </div>
                </div>

                <!-- Von -->
                <div style="margin-bottom:20px;">
                    <p style="margin:0 0 8px 0;font-size:14px;font-weight:600;color:#003399;">Von:</p>
                    <div style="background:#f9fafb;padding:12px;border-left:3px solid #FFD700;">
                        <strong>E-Mail:</strong> {{ $contactMessage->email }}
                        @if($contactMessage->user)
                            <br><strong>User-ID:</strong> {{ $contactMessage->user->id }}
                            <br><strong>Name:</strong> {{ $contactMessage->user->name }}
                        @endif
                    </div>
                </div>

                @if($contactMessage->hermine_contact)
                <!-- Hermine-Kontakt -->
                <div style="margin-bottom:20px;">
                    <p style="margin:0 0 8px 0;font-size:14px;font-weight:600;color:#003399;">Hermine-Kontakt gewünscht:</p>
                    <div style="background:#f0fdf4;padding:12px;border-left:3px solid #22c55e;">
                        <strong style="color:#16a34a;">Ja, über Hermine kontaktieren</strong><br>
                        <strong>Vorname:</strong> {{ $contactMessage->vorname }}<br>
                        <strong>Nachname:</strong> {{ $contactMessage->nachname }}<br>
                        <strong>Ortsverband:</strong> {{ $contactMessage->ortsverband }}
                    </div>
                </div>
                @endif

                @if($contactMessage->type === 'bug' && $contactMessage->error_location)
                <!-- Fehler-Location -->
                <div style="margin-bottom:20px;">
                    <p style="margin:0 0 8px 0;font-size:14px;font-weight:600;color:#003399;">Fehler aufgetreten bei:</p>
                    <div style="background:#fef2f2;padding:12px;border-left:3px solid #ef4444;color:#991b1b;">
                        {{ $contactMessage->error_location }}
                    </div>
                </div>
                @endif

                <!-- Nachricht -->
                <div style="margin-bottom:20px;">
                    <p style="margin:0 0 8px 0;font-size:14px;font-weight:600;color:#003399;">Nachricht:</p>
                    <div style="background:#ffffff;padding:20px;border:2px solid #003399;border-radius:8px;white-space:pre-wrap;word-wrap:break-word;font-size:15px;line-height:1.6;color:#1a202c;">{{ $contactMessage->message }}</div>
                </div>

            </div>

            <!-- Footer -->
            <div style="padding:20px;background:#f9fafb;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="margin:0;font-size:12px;color:#6b7280;">
                    Diese Nachricht wurde über das Kontaktformular der THW-Trainer App gesendet.
                </p>
                <p style="margin:8px 0 0 0;font-size:12px;color:#999;">
                    © {{ date('Y') }} THW-Trainer |
                    <a href="https://thw-trainer.de" style="color:#003399;text-decoration:none;">thw-trainer.de</a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>
