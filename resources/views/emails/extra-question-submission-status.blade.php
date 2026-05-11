<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status deiner Zusatz-Frage - THW Trainer</title>
</head>
@php
    $status = $submission->status;
    $headline = match($status) {
        'approved' => 'Deine Zusatz-Frage wurde übernommen!',
        'rejected' => 'Deine Zusatz-Frage wurde abgelehnt',
        'changed'  => 'Deine Zusatz-Frage wurde mit Änderungen übernommen',
        default    => 'Status deiner Zusatz-Frage',
    };
    $eyebrow = match($status) {
        'approved' => 'Übernommen',
        'rejected' => 'Abgelehnt',
        'changed'  => 'Mit Änderungen übernommen',
        default    => 'Status-Update',
    };
    $accent = match($status) {
        'approved' => '#16a34a',
        'rejected' => '#ef4444',
        'changed'  => '#d97706',
        default    => '#00337F',
    };
@endphp
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;">
    <div style="background:#f0f2f5;padding:32px 16px;">
        <div style="max-width:600px;margin:0 auto;">

            <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <tr>
                        <td width="32" height="32" align="center" valign="middle" style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;text-align:center;vertical-align:middle;">
                            <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW" width="18" height="18" style="display:block;margin:0 auto;width:18px;height:18px;border:0;">
                        </td>
                        <td valign="middle" style="vertical-align:middle;padding-left:10px;color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</td>
                    </tr>
                </table>
            </div>

            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid {{ $accent }};box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">{{ $eyebrow }}</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">{{ $headline }}</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $submission->user->name }}</strong>,
                </p>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    @if($status === 'approved')
                        wir haben deine eingereichte Zusatz-Frage erfolgreich in den Fragenkatalog übernommen. Vielen Dank für deinen Beitrag!
                    @elseif($status === 'changed')
                        wir haben deine eingereichte Zusatz-Frage übernommen – allerdings mit ein paar Anpassungen. Danke trotzdem für deinen Beitrag!
                    @elseif($status === 'rejected')
                        leider haben wir deine eingereichte Zusatz-Frage nicht in den Fragenkatalog aufgenommen.
                    @else
                        es gibt eine Aktualisierung zu deiner eingereichten Zusatz-Frage.
                    @endif
                </p>

                <div style="background:#f8fafc;border-radius:0.75rem;padding:14px 18px;margin-bottom:14px;border:1px solid rgba(0,51,127,0.10);">
                    <div style="font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Deine Frage</div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;line-height:1.5;">{{ \Illuminate\Support\Str::limit($submission->frage, 220) }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:6px;">
                        {{ $submission->typLabel() }} · Lernabschnitt {{ $submission->lernabschnitt }}
                    </div>
                </div>

                @if($submission->admin_notes)
                    <div style="background:#fff7ed;border-radius:0.75rem;padding:14px 18px;margin-bottom:18px;border:1px solid rgba(217,119,6,0.20);">
                        <div style="font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:#92400e;margin-bottom:6px;">Hinweis vom Admin</div>
                        <div style="font-size:13px;color:#1f2937;line-height:1.6;white-space:pre-wrap;">{{ $submission->admin_notes }}</div>
                    </div>
                @endif

                <div style="text-align:center;margin:22px 0 6px;">
                    <a href="{{ route('zusatzfragen-vorschlagen.index') }}"
                       style="display:inline-block;background:#00337F;color:#ffffff;padding:11px 22px;border-radius:0.5rem;text-decoration:none;font-weight:600;font-size:14px;">
                        Meine Einreichungen ansehen
                    </a>
                </div>
            </div>

            <div style="padding:18px;background:transparent;text-align:center;">
                <p style="margin:0;font-size:12px;color:#94a3b8;">
                    © {{ date('Y') }} THW-Trainer ·
                    <a href="https://thw-trainer.de" style="color:#00337F;text-decoration:none;">thw-trainer.de</a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>
