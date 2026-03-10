<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prüfungs-Feedback - THW Trainer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background:#0a0e1a;min-height:100vh;">

    <div style="max-width:680px;margin:0 auto;padding:32px 16px;">

        <!-- Logo -->
        <div style="text-align:center;margin-bottom:32px;">
            <a href="/">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" style="max-height:48px;width:auto;" />
            </a>
        </div>

        @if(isset($justSubmitted) && $justSubmitted)
            <!-- Danke-Nachricht nach Absenden -->
            <div class="glass-gold" style="padding:40px 32px;border-radius:16px;text-align:center;">
                <div style="font-size:28px;font-weight:700;color:#FFD700;margin-bottom:16px;">
                    Vielen Dank für dein Feedback!
                </div>
                @if($feedback->passed)
                    <p style="font-size:18px;color:#22c55e;font-weight:600;margin-bottom:12px;">
                        Herzlichen Glückwunsch zur bestandenen Prüfung!
                    </p>
                @else
                    <p style="font-size:18px;color:#f59e0b;font-weight:600;margin-bottom:12px;">
                        Kopf hoch - beim nächsten Mal klappt es bestimmt!
                    </p>
                @endif
                <p style="font-size:15px;color:rgba(255,255,255,0.7);line-height:1.6;margin-top:16px;">
                    Deine Rückmeldung hilft uns, den THW Trainer weiterzuentwickeln und anderen Helfern zu zeigen, wie gut die Vorbereitung funktioniert.
                </p>
                <div style="margin-top:28px;">
                    <a href="https://thw-trainer.de" class="btn-primary" style="display:inline-block;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600;">
                        Zurück zum THW Trainer
                    </a>
                </div>
            </div>

        @elseif($alreadySubmitted)
            <!-- Bereits ausgefüllt -->
            <div class="glass" style="padding:40px 32px;border-radius:16px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#FFD700;margin-bottom:16px;">
                    Feedback bereits abgegeben
                </div>
                <p style="font-size:16px;color:rgba(255,255,255,0.7);line-height:1.6;">
                    Du hast bereits Feedback zu deiner Prüfung gegeben. Vielen Dank dafür!
                </p>
                <div style="margin-top:28px;">
                    <a href="https://thw-trainer.de" class="btn-primary" style="display:inline-block;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600;">
                        Zurück zum THW Trainer
                    </a>
                </div>
            </div>

        @else
            <!-- Feedback-Formular -->
            <div class="glass-gold" style="padding:32px;border-radius:16px;margin-bottom:24px;">
                <h1 style="font-size:26px;font-weight:700;color:#FFD700;margin:0 0 8px 0;">
                    Prüfungs-Feedback
                </h1>
                <p style="font-size:15px;color:rgba(255,255,255,0.7);margin:0;">
                    Hallo <strong style="color:#fff;">{{ $feedback->user->name }}</strong>, erzähl uns wie deine Prüfung gelaufen ist!
                </p>
            </div>

            <!-- Info-Box: Warum Feedback -->
            <div class="glass" style="padding:20px 24px;border-radius:12px;margin-bottom:24px;border-left:3px solid #FFD700;">
                <p style="font-size:14px;color:rgba(255,255,255,0.8);line-height:1.6;margin:0;">
                    <strong style="color:#FFD700;">Warum ist dein Feedback wichtig?</strong><br>
                    Wir erheben die Bestehens- und Durchfallstatistik, um anderen THW-Helfern transparent zu zeigen,
                    wie gut die Vorbereitung mit dem THW Trainer funktioniert. Deine Bewertung hilft außerdem anderen
                    Helfern zu sehen, wie zufrieden Nutzer mit unserer Plattform sind.
                </p>
            </div>

            <form action="{{ route('exam-feedback.store', $feedback->token) }}" method="POST">
                @csrf

                <!-- Prüfung bestanden? -->
                <div class="glass" style="padding:24px;border-radius:12px;margin-bottom:20px;">
                    <label style="font-size:16px;font-weight:600;color:#fff;display:block;margin-bottom:16px;">
                        Hast du die Prüfung bestanden?
                    </label>

                    <div style="display:flex;gap:12px;">
                        <label style="flex:1;cursor:pointer;">
                            <input type="radio" name="passed" value="1" required style="display:none;" id="passed-yes">
                            <div id="option-yes" style="background:rgba(34,197,94,0.1);border:2px solid rgba(34,197,94,0.3);border-radius:10px;padding:18px;text-align:center;transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(34,197,94,0.6)'" onmouseout="if(!document.getElementById('passed-yes').checked) this.style.borderColor='rgba(34,197,94,0.3)'">
                                <div style="font-size:18px;font-weight:700;color:#22c55e;">Bestanden</div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:4px;">Prüfung erfolgreich</div>
                            </div>
                        </label>

                        <label style="flex:1;cursor:pointer;">
                            <input type="radio" name="passed" value="0" required style="display:none;" id="passed-no">
                            <div id="option-no" style="background:rgba(239,68,68,0.1);border:2px solid rgba(239,68,68,0.3);border-radius:10px;padding:18px;text-align:center;transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(239,68,68,0.6)'" onmouseout="if(!document.getElementById('passed-no').checked) this.style.borderColor='rgba(239,68,68,0.3)'">
                                <div style="font-size:18px;font-weight:700;color:#ef4444;">Nicht bestanden</div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:4px;">Leider durchgefallen</div>
                            </div>
                        </label>
                    </div>

                    @error('passed')
                        <p style="color:#ef4444;font-size:14px;margin-top:8px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Feedback Text -->
                <div class="glass" style="padding:24px;border-radius:12px;margin-bottom:20px;">
                    <label for="feedback" style="font-size:16px;font-weight:600;color:#fff;display:block;margin-bottom:12px;">
                        Wie war deine Erfahrung? <span style="color:rgba(255,255,255,0.4);font-weight:400;">(optional)</span>
                    </label>
                    <textarea
                        name="feedback"
                        id="feedback"
                        rows="4"
                        maxlength="2000"
                        placeholder="Erzähl uns von deiner Prüfung... War die Vorbereitung mit dem THW Trainer hilfreich? Was können wir verbessern?"
                        style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:14px;color:#fff;font-size:15px;line-height:1.6;resize:vertical;box-sizing:border-box;"
                    >{{ old('feedback') }}</textarea>
                    @error('feedback')
                        <p style="color:#ef4444;font-size:14px;margin-top:8px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Veröffentlichungsmodus -->
                <div class="glass" style="padding:24px;border-radius:12px;margin-bottom:24px;">
                    <label style="font-size:16px;font-weight:600;color:#fff;display:block;margin-bottom:16px;">
                        Veröffentlichung deiner Bewertung
                    </label>

                    <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;padding:12px;border-radius:8px;background:rgba(255,255,255,0.03);margin-bottom:10px;">
                        <input type="radio" name="publish_mode" value="anonymous" checked style="margin-top:3px;accent-color:#FFD700;">
                        <div>
                            <div style="font-size:15px;font-weight:600;color:#fff;">Anonym veröffentlichen</div>
                            <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:2px;">Dein Feedback wird ohne deinen Namen angezeigt</div>
                        </div>
                    </label>

                    <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;padding:12px;border-radius:8px;background:rgba(255,255,255,0.03);">
                        <input type="radio" name="publish_mode" value="named" style="margin-top:3px;accent-color:#FFD700;">
                        <div>
                            <div style="font-size:15px;font-weight:600;color:#fff;">Mit Klarnamen veröffentlichen</div>
                            <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:2px;">Dein Feedback wird mit deinem Namen ({{ $feedback->user->name }}) angezeigt</div>
                        </div>
                    </label>

                    @error('publish_mode')
                        <p style="color:#ef4444;font-size:14px;margin-top:8px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statistik-Hinweis -->
                <div style="padding:16px 20px;border-radius:10px;margin-bottom:24px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);">
                    <p style="font-size:13px;color:rgba(255,255,255,0.5);line-height:1.6;margin:0;">
                        <strong style="color:rgba(255,255,255,0.7);">Hinweis zur Datenverwendung:</strong>
                        Deine Angabe, ob du bestanden hast, fließt in unsere Bestehensstatistik ein. Diese Statistik zeigt
                        anderen THW-Helfern, wie erfolgreich die Prüfungsvorbereitung mit dem THW Trainer ist.
                        Dein Feedback und deine Bewertung werden veröffentlicht, damit andere Helfer sehen können,
                        wie zufrieden unsere Nutzer sind - je nach deiner Wahl anonym oder mit Klarnamen.
                    </p>
                </div>

                <!-- Submit -->
                <div style="text-align:center;">
                    <button type="submit" class="btn-primary" style="padding:14px 48px;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;border:none;">
                        Feedback absenden
                    </button>
                </div>
            </form>
        @endif

        <!-- Footer -->
        <div style="text-align:center;margin-top:40px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.08);">
            <p style="font-size:12px;color:rgba(255,255,255,0.3);line-height:1.5;margin:0;">
                © {{ date('Y') }} THW-Trainer.de |
                <a href="https://thw-trainer.de/impressum" style="color:rgba(255,255,255,0.3);text-decoration:none;">Impressum</a> |
                <a href="https://thw-trainer.de/datenschutz" style="color:rgba(255,255,255,0.3);text-decoration:none;">Datenschutz</a>
            </p>
        </div>
    </div>

    <script>
        // Radio-Button visuelle Selektion
        document.querySelectorAll('input[name="passed"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const yesOption = document.getElementById('option-yes');
                const noOption = document.getElementById('option-no');

                if (this.value === '1') {
                    yesOption.style.borderColor = '#22c55e';
                    yesOption.style.background = 'rgba(34,197,94,0.2)';
                    noOption.style.borderColor = 'rgba(239,68,68,0.3)';
                    noOption.style.background = 'rgba(239,68,68,0.1)';
                } else {
                    noOption.style.borderColor = '#ef4444';
                    noOption.style.background = 'rgba(239,68,68,0.2)';
                    yesOption.style.borderColor = 'rgba(34,197,94,0.3)';
                    yesOption.style.background = 'rgba(34,197,94,0.1)';
                }
            });
        });
    </script>
</body>
</html>
