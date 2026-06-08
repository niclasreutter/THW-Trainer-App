@extends('layouts.landing')

@section('title', 'Datenschutzerklärung - THW Trainer')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8 lg:p-12">
        <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-8">Datenschutzerklärung</h1>

        <div class="space-y-8 text-slate-600">
            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">1. Verantwortlicher</h2>
                <div class="ml-4">
                    <p>Verantwortlich für die Datenverarbeitung auf dieser Website ist:</p>
                    <div class="mt-2">
                        <p>Niclas Reutter</p>
                        <p>Ringstraße 10</p>
                        <p>88719 Stetten</p>
                        <p>Deutschland</p>
                        <p>E-Mail: <a href="mailto:niclas@thw-trainer.de" class="text-thw-blue hover:text-blue-800 underline">niclas@thw-trainer.de</a></p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">2. Allgemeines zur Datenverarbeitung</h2>
                <div class="ml-4">
                    <p>Diese Datenschutzerklärung klärt Sie über die Art, den Umfang und den Zweck der Verarbeitung von personenbezogenen Daten auf unserer Website auf. Personenbezogene Daten sind alle Daten, die auf Sie persönlich beziehbar sind, z.B. Name, Adresse, E-Mail-Adressen, Nutzerverhalten.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">3. Erhebung und Verarbeitung personenbezogener Daten</h2>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.1 Registrierung und Benutzerkonto</h3>
                    <p class="mb-4">Zur Nutzung der Lerninhalte ist eine Registrierung erforderlich. Dabei erheben wir folgende Daten:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Name (Vorname und Nachname)</li>
                        <li>E-Mail-Adresse</li>
                        <li>Passwort (verschlüsselt gespeichert)</li>
                    </ul>
                    <p class="mb-4">Diese Daten werden zur Benutzerverwaltung, Authentifizierung und zur Bereitstellung der personalisierten Lerninhalte verwendet.</p>

                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.2 Lernfortschritt und Prüfungsergebnisse</h3>
                    <p class="mb-4">Zur Verfolgung Ihres Lernfortschritts speichern wir:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Beantwortete Fragen und deren Ergebnisse</li>
                        <li>Prüfungsergebnisse und -zeiten</li>
                        <li>Falsch beantwortete Fragen für gezieltes Üben</li>
                        <li>Anzahl bestandener Prüfungen</li>
                        <li>Gesammelte Punkte, erreichte Level und Achievements</li>
                        <li>Lernsträhnen (aufeinanderfolgende Lerntage)</li>
                        <li>Lesezeichen und als schwierig markierte Fragen</li>
                    </ul>
                    <p class="mb-4">Diese Daten dienen ausschließlich der Verbesserung Ihres Lernerfolgs und werden nicht an Dritte weitergegeben.</p>

                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.3 E-Mail-Benachrichtigungen (Opt-In)</h3>
                    <p class="mb-4">Sie können freiwillig der Zusendung von E-Mail-Benachrichtigungen zustimmen. Diese umfassen:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Informationen zu Ihrem Lernfortschritt</li>
                        <li>Hinweise auf neue Features und Funktionen</li>
                        <li>Wichtige System- und Sicherheitsinformationen</li>
                        <li>Erinnerungen bei längerer Inaktivität (optional)</li>
                    </ul>
                    <p class="mb-4">Die Verarbeitung erfolgt auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Sie können diese Einwilligung jederzeit in Ihren Profileinstellungen widerrufen.</p>

                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.4 Öffentliches Leaderboard (Opt-In)</h3>
                    <p class="mb-4">Sie können freiwillig der Teilnahme am öffentlichen Leaderboard zustimmen. Bei Zustimmung werden folgende Daten für andere registrierte Nutzer sichtbar:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Ihr Benutzername (Vor- und Nachname)</li>
                        <li>Ihre Gesamtpunktzahl</li>
                        <li>Ihre wöchentliche Punktzahl</li>
                        <li>Ihre Position im Ranking</li>
                    </ul>
                    <p class="mb-4">Die Verarbeitung erfolgt auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Sie können diese Einwilligung jederzeit in Ihren Profileinstellungen widerrufen.</p>

                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.5 Lernsessions (Opt-In pro Session)</h3>
                    <p class="mb-4">Sie können an geplanten Lernsessions teilnehmen. Beim Beitritt zu einer Session können Sie freiwillig der Anzeige Ihrer Daten im Session-Ranking zustimmen. Bei Zustimmung werden folgende Daten für andere Teilnehmer dieser Session sichtbar:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Ihr Benutzername</li>
                        <li>Ihre während der Session gesammelten Punkte (XP)</li>
                        <li>Ihre Genauigkeit und Position im Ranking</li>
                    </ul>
                    <p class="mb-4">Die Einwilligung gilt ausschließlich für die jeweilige Session-Instanz und wird nicht dauerhaft oder für andere Sessions übernommen. Ohne Zustimmung nehmen Sie anonym teil.</p>
                    <p class="mb-4">Die Verarbeitung erfolgt auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO).</p>
                    <p class="mb-4"><strong>Liga-Ranglisten in der Admin-Ansicht:</strong> Auch in der internen Admin-Übersicht der Liga-Ranglisten werden Teilnehmer, die nicht ausdrücklich in die namentliche Anzeige im Leaderboard eingewilligt haben, als „Anonymer Teilnehmer" angezeigt.</p>

                    <h3 class="text-lg font-medium text-slate-800 mb-2">3.6 Nutzerverwaltung durch Administratoren</h3>
                    <p class="mb-4">Zur Sicherstellung eines ordnungsgemäßen Betriebs der Plattform haben berechtigte Administratoren <strong>lesenden</strong> Zugriff auf Ihre Kontodaten. Dieser Zugriff erfolgt ausschließlich zweckgebunden und ist auf das für die Verwaltung notwendige Maß beschränkt. Administratoren können folgende Daten einsehen:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Name und E-Mail-Adresse</li>
                        <li>Benutzerrolle</li>
                        <li>Punkte (XP), Level und Lernfortschritt</li>
                        <li>E-Mail-Verifizierungsstatus</li>
                        <li>Status der Newsletter-Zustimmung</li>
                    </ul>
                    <p class="mb-4"><strong>Administrative Aktionen sind beschränkt auf:</strong></p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Bearbeitung von Stammdaten (Name, E-Mail, Rolle, Punkte)</li>
                        <li>Manuelle E-Mail-Verifizierung</li>
                        <li>Löschung des Kontos – nur auf Ihren Antrag oder bei Verstoß gegen die Nutzungsbedingungen, und nur unter Angabe einer zwingenden Begründung</li>
                    </ul>
                    <p class="mb-4">Eine Manipulation Ihres Lernfortschritts durch Administratoren (z.&nbsp;B. Markieren als gemeistert, Zurücksetzen einzelner Fragen, Reset des gesamten Fortschritts) ist <strong>nicht mehr möglich</strong>.</p>

                    <p class="mb-4"><strong>Protokollierung (Audit-Log):</strong> Sämtliche Aktionen, die ein Administrator an oder mit Ihren Daten vornimmt, werden lückenlos protokolliert – einschließlich reiner Lese-Zugriffe auf Detaildaten. Erfasst werden:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Art der Aktion (z.&nbsp;B. Stammdaten-Änderung, E-Mail-Verifizierung, Einsicht in den Lernfortschritt, Einsicht in den XP-Verlauf, Konto-Löschung)</li>
                        <li>Alter und neuer Wert bei Stammdaten-Änderungen</li>
                        <li>Bei Konto-Löschungen: der vom Administrator angegebene Lösch-Grund</li>
                        <li>Zeitstempel der Aktion</li>
                        <li>Name des verantwortlichen Administrators</li>
                        <li>IP-Adresse des Administrators (zur Nachvollziehbarkeit)</li>
                    </ul>
                    <p class="mb-4"><strong>Transparenz:</strong> Sie können alle administrativen Aktionen, die Ihr Konto betreffen, jederzeit in Ihrem Profil unter dem Abschnitt „Verwaltungsänderungen" einsehen – inklusive der bloßen Einsichtnahme durch Administratoren.</p>
                    <p class="mb-4">Die Verarbeitung erfolgt auf Grundlage unseres berechtigten Interesses an einer sicheren, korrekten und nachvollziehbaren Verwaltung der Plattform (Art. 6 Abs. 1 lit. f DSGVO) sowie zur Erfüllung unserer Nachweis- und Rechenschaftspflichten (Art. 5 Abs. 2 DSGVO).</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">4. Rechtsgrundlage der Datenverarbeitung</h2>
                <div class="ml-4">
                    <p>Die Verarbeitung Ihrer personenbezogenen Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung) sowie Art. 6 Abs. 1 lit. f DSGVO (berechtigte Interessen zur Bereitstellung der Lernplattform).</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">5. Speicherdauer</h2>
                <div class="ml-4">
                    <p class="mb-4">Ihre personenbezogenen Daten werden gelöscht, sobald sie für die Zwecke, für die sie erhoben wurden, nicht mehr erforderlich sind, es sei denn, gesetzliche Aufbewahrungsfristen erfordern eine längere Speicherung.</p>
                    <p class="mb-4"><strong>Bei Löschung Ihres Kontos</strong> – egal ob durch Sie selbst oder durch einen Administrator – werden alle direkt zuordenbaren personenbezogenen Daten unwiderruflich entfernt. Konkret gelöscht werden u.&nbsp;a.: Lernfortschritt, XP-Verlauf, Benachrichtigungen, Push-Abonnements, Lootboxen, Shop-Käufe, Umfrage- und Prüfungs-Feedback sowie aktive Sitzungen (Sessions).</p>
                    <p class="mb-4"><strong>Anonymisiert erhalten</strong> bleiben statistische Daten zu beantworteten Fragen (z.&nbsp;B. „Frage X wurde von n Personen falsch beantwortet"). Diese Daten enthalten nach der Löschung keinen Personenbezug mehr und ermöglichen keine Re-Identifikation.</p>
                    <p>Zur Erfüllung unserer Rechenschaftspflicht (Art. 5 Abs. 2 DSGVO) wird die Löschung selbst in einem separaten Compliance-Protokoll dokumentiert. Dieses Protokoll enthält ausschließlich den Zeitpunkt der Löschung, die ID des durchführenden Administrators, dessen IP-Adresse und den Lösch-Grund – es enthält <strong>keinerlei personenbezogene Daten zum gelöschten Konto</strong>. Diese Protokoll-Einträge werden für drei Jahre aufbewahrt und anschließend gelöscht.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">6. Cookies</h2>
                <div class="ml-4">
                    <p class="mb-4">Diese Website verwendet nur technisch notwendige Cookies, die für den Betrieb der Seite erforderlich sind:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li><strong>Session-Cookie:</strong> Zur Aufrechterhaltung Ihrer Anmeldung während der Nutzung</li>
                        <li><strong>CSRF-Token:</strong> Zum Schutz vor Cross-Site-Request-Forgery-Angriffen</li>
                    </ul>
                    <p>Diese Cookies werden automatisch gelöscht, wenn Sie Ihren Browser schließen oder sich abmelden. Marketing- oder Tracking-Cookies werden nicht verwendet.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">7. Externe Dienste und Drittanbieter</h2>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-slate-800 mb-2">7.1 Fonts Bunny</h3>
                    <p class="mb-4">Diese Website lädt Schriftarten von Fonts Bunny (fonts.bunny.net) nach. Fonts Bunny ist ein datenschutzfreundlicher Font-Service, der keine personenbezogenen Daten sammelt oder speichert.</p>
                    <p class="mb-4"><strong>Anbieter:</strong> BunnyWay SRL, Strada Tudor Vladimirescu 22, 500036 Brașov, Rumänien</p>
                    <p class="mb-4"><strong>Datenschutzerklärung:</strong> <a href="https://fonts.bunny.net/privacy" class="text-thw-blue hover:text-blue-800 underline" target="_blank" rel="noopener">https://fonts.bunny.net/privacy</a></p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">8. Datensicherheit</h2>
                <div class="ml-4">
                    <p>Wir setzen technische und organisatorische Sicherheitsmaßnahmen ein, um Ihre Daten gegen zufällige oder vorsätzliche Manipulationen, Verlust, Zerstörung oder Zugriff unberechtigter Personen zu schützen. Alle Passwörter werden verschlüsselt gespeichert und die Datenübertragung erfolgt über eine sichere SSL-Verbindung.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">9. Ihre Rechte</h2>
                <div class="ml-4">
                    <p class="mb-4">Sie haben folgende Rechte bezüglich Ihrer personenbezogenen Daten:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-2">
                        <li><strong>Auskunftsrecht (Art. 15 DSGVO):</strong> Sie können Auskunft über Ihre von uns verarbeiteten personenbezogenen Daten verlangen</li>
                        <li><strong>Berichtigungsrecht (Art. 16 DSGVO):</strong> Sie können die Berichtigung unrichtiger Daten verlangen</li>
                        <li><strong>Löschungsrecht (Art. 17 DSGVO):</strong> Sie können die Löschung Ihrer Daten verlangen</li>
                        <li><strong>Einschränkungsrecht (Art. 18 DSGVO):</strong> Sie können die Einschränkung der Verarbeitung verlangen</li>
                        <li><strong>Datenübertragbarkeit (Art. 20 DSGVO):</strong> Sie können Ihre Daten in einem strukturierten Format erhalten</li>
                        <li><strong>Widerspruchsrecht (Art. 21 DSGVO):</strong> Sie können der Verarbeitung widersprechen</li>
                    </ul>
                    <p>Zur Ausübung Ihrer Rechte wenden Sie sich bitte an: <a href="mailto:niclas@thw-trainer.de" class="text-thw-blue hover:text-blue-800 underline">niclas@thw-trainer.de</a></p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">10. Beschwerderecht</h2>
                <div class="ml-4">
                    <p>Sie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über unsere Verarbeitung personenbezogener Daten zu beschweren.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">11. Weitergabe an Dritte</h2>
                <div class="ml-4">
                    <p>Eine Weitergabe Ihrer personenbezogenen Daten an Dritte findet nicht statt, es sei denn, dies ist zur Vertragsabwicklung erforderlich oder Sie haben ausdrücklich eingewilligt.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">12. Änderungen der Datenschutzerklärung</h2>
                <div class="ml-4">
                    <p>Diese Datenschutzerklärung kann bei Änderungen der Website oder bei Änderungen der rechtlichen Bestimmungen angepasst werden. Die aktuelle Version ist stets auf dieser Seite verfügbar.</p>
                </div>
            </section>

            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-sm text-slate-500">Stand: 27. Mai 2026</p>
                <p class="text-xs text-slate-400 mt-2">Letzte Änderung: Beschränkung administrativer Aktionen auf reine Stammdaten-Pflege, Audit-Log für Lese-Zugriffe, DSGVO-konforme Konto-Löschung mit anonymisiertem Compliance-Protokoll und Anonymisierung in Admin-Ranglisten.</p>
            </div>
        </div>
    </div>
</div>
@endsection
