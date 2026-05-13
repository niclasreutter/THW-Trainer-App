<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehlermeldung zugewiesen - THW Trainer</title>
</head>
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

            <div style="background:#ffffff;padding:28px 24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Fehlermeldung zugewiesen</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">Du hast eine neue Fehlermeldung</div>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    Hallo <strong style="color:#0f172a;">{{ $assignee->name }}</strong>,
                </p>

                <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.6;">
                    <strong style="color:#0f172a;">{{ $assignedBy->name }}</strong> hat dir die
                    Fehlermeldung <strong style="color:#0f172a;">FM-{{ str_pad($issueId, 3, '0', STR_PAD_LEFT) }}</strong> zugewiesen.
                </p>

                @if($questionText)
                    <div style="background:#f8fafc;border-radius:0.75rem;padding:14px 18px;margin-bottom:18px;border:1px solid rgba(0,51,127,0.10);">
                        <div style="font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Betroffene Frage</div>
                        <div style="font-size:14px;font-weight:600;color:#0f172a;line-height:1.5;">{{ \Illuminate\Support\Str::limit($questionText, 240) }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:6px;">
                            {{ $issueType === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}
                        </div>
                    </div>
                @endif

                <div style="text-align:center;margin:24px 0 8px;">
                    <a href="{{ $issueUrl }}" style="background:linear-gradient(135deg,#00337F,#0055cc);color:#fff;padding:12px 32px;border-radius:0.5rem;text-decoration:none;font-weight:700;font-size:13px;display:inline-block;box-shadow:0 4px 15px rgba(0,51,127,0.3);">Fehlermeldung öffnen</a>
                </div>

                <p style="margin:18px 0 0 0;font-size:12px;color:#94a3b8;line-height:1.5;text-align:center;">
                    Du erhältst diese E-Mail, weil dir eine Fehlermeldung im THW-Trainer Admin-Bereich zugewiesen wurde.
                </p>
            </div>

            <div style="text-align:center;padding:18px 0;font-size:11px;color:#94a3b8;">
                © {{ date('Y') }} THW-Trainer · <a href="{{ url('/') }}" style="color:#0055cc;text-decoration:none;">thw-trainer.de</a>
            </div>
        </div>
    </div>
</body>
</html>
