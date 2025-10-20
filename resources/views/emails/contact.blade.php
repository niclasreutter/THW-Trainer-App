<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1e3a8a;
            color: #fbbf24;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .label {
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .value {
            background-color: white;
            padding: 10px;
            border-left: 3px solid #fbbf24;
            margin-bottom: 15px;
        }
        .message-box {
            background-color: white;
            padding: 20px;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .badge-feedback { background-color: #dbeafe; color: #1e40af; }
        .badge-feature { background-color: #fef3c7; color: #92400e; }
        .badge-bug { background-color: #fee2e2; color: #991b1b; }
        .badge-other { background-color: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Neue Kontaktanfrage</h1>
            <p style="margin: 0;">THW-Trainer App</p>
        </div>
        
        <div class="content">
            <div class="label">Kategorie:</div>
            <div class="value">
                @php
                    $badgeClass = match($contactMessage->type) {
                        'feedback' => 'badge-feedback',
                        'feature' => 'badge-feature',
                        'bug' => 'badge-bug',
                        default => 'badge-other',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $contactMessage->type_label }}</span>
            </div>

            <div class="label">Von:</div>
            <div class="value">
                <strong>E-Mail:</strong> {{ $contactMessage->email }}
                @if($contactMessage->user)
                    <br><strong>User-ID:</strong> {{ $contactMessage->user->id }}
                    <br><strong>Name:</strong> {{ $contactMessage->user->name }}
                @endif
            </div>

            @if($contactMessage->hermine_contact)
            <div class="label">Hermine-Kontakt gewünscht:</div>
            <div class="value">
                <strong>✅ Ja, über Hermine kontaktieren</strong><br>
                <strong>Vorname:</strong> {{ $contactMessage->vorname }}<br>
                <strong>Nachname:</strong> {{ $contactMessage->nachname }}<br>
                <strong>Ortsverband:</strong> {{ $contactMessage->ortsverband }}
            </div>
            @endif

            @if($contactMessage->type === 'bug' && $contactMessage->error_location)
            <div class="label">🐛 Fehler aufgetreten bei:</div>
            <div class="value">{{ $contactMessage->error_location }}</div>
            @endif

            <div class="label">Nachricht:</div>
            <div class="message-box">{{ $contactMessage->message }}</div>
        </div>

        <div class="footer">
            <p>Diese Nachricht wurde über das Kontaktformular der THW-Trainer App gesendet.</p>
            <p>© {{ date('Y') }} THW-Trainer • <a href="https://thw-trainer.de">thw-trainer.de</a></p>
        </div>
    </div>
</body>
</html>
