@extends('layouts.app')

@section('title', 'Datenschutzerklärung - THW Trainer')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Datenschutzerklärung</h1>
        
        <div class="space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">1. Verantwortlicher</h2>
                <div class="ml-4">
                    <p>Verantwortlich für die Datenverarbeitung auf dieser Website ist:</p>
                    <div class="mt-2">
                        <p>Niclas Reutter</p>
                        <p>Ringstraße 10</p>
                        <p>88719 Stetten</p>
                        <p>Deutschland</p>
                        <p>E-Mail: <a href="mailto:niclas@thw-trainer.de" class="text-blue-600 hover:text-blue-800 underline">niclas@thw-trainer.de</a></p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">2. Allgemeines zur Datenverarbeitung</h2>
                <div class="ml-4">
                    <p>Diese Datenschutzerklärung klärt Sie über die Art, den Umfang und den Zweck der Verarbeitung von personenbezogenen Daten auf unserer Website auf. Personenbezogene Daten sind alle Daten, die auf Sie persönlich beziehbar sind, z.B. Name, Adresse, E-Mail-Adressen, Nutzerverhalten.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">3. Erhebung und Verarbeitung personenbezogener Daten</h2>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">3.1 Registrierung und Benutzerkonto</h3>
                    <p class="mb-4">Zur Nutzung der Lerninhalte ist eine Registrierung erforderlich. Dabei erheben wir folgende Daten:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Name (Vorname und Nachname)</li>
                        <li>E-Mail-Adresse</li>
                        <li>Passwort (verschlüsselt gespeichert)</li>
                    </ul>
                    <p class="mb-4">Diese Daten werden zur Benutzerverwaltung, Authentifizierung und zur Bereitstellung der personalisierten Lerninhalte verwendet.</p>
                    
                    <h3 class="text-lg font-medium text-gray-800 mb-2">3.2 Lernfortschritt und Prüfungsergebnisse</h3>
                    <p class="mb-4">Zur Verfolgung Ihres Lernfortschritts speichern wir:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-1">
                        <li>Beantwortete Fragen und deren Ergebnisse</li>
                        <li>Prüfungsergebnisse und -zeiten</li>
                        <li>Falsch beantwortete Fragen für gezieltes Üben</li>
                        <li>Anzahl bestandener Prüfungen</li>
                    </ul>
                    <p>Diese Daten dienen ausschließlich der Verbesserung Ihres Lernerfolgs und werden nicht an Dritte weitergegeben.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">4. Rechtsgrundlage der Datenverarbeitung</h2>
                <div class="ml-4">
                    <p>Die Verarbeitung Ihrer personenbezogenen Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung) sowie Art. 6 Abs. 1 lit. f DSGVO (berechtigte Interessen zur Bereitstellung der Lernplattform).</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">5. Speicherdauer</h2>
                <div class="ml-4">
                    <p>Ihre personenbezogenen Daten werden gelöscht, sobald sie für die Zwecke, für die sie erhoben wurden, nicht mehr erforderlich sind. Benutzerdaten werden nach Löschung des Benutzerkontos entfernt, es sei denn, gesetzliche Aufbewahrungsfristen erfordern eine längere Speicherung.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">6. Cookies</h2>
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
                <h2 class="text-xl font-semibold text-gray-800 mb-3">7. Externe Dienste und Drittanbieter</h2>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">7.1 Fonts Bunny</h3>
                    <p class="mb-4">Diese Website lädt Schriftarten von Fonts Bunny (fonts.bunny.net) nach. Fonts Bunny ist ein datenschutzfreundlicher Font-Service, der keine personenbezogenen Daten sammelt oder speichert. Beim Laden der Schriftarten wird lediglich Ihre IP-Adresse temporär übertragen, um die Schriftarten auszuliefern. Diese Daten werden nicht gespeichert oder für andere Zwecke verwendet.</p>
                    <p class="mb-4"><strong>Anbieter:</strong> BunnyWay SRL, Strada Tudor Vladimirescu 22, 500036 Brașov, Rumänien</p>
                    <p class="mb-4"><strong>Datenschutzerklärung:</strong> <a href="https://fonts.bunny.net/privacy" class="text-blue-600 hover:text-blue-800 underline" target="_blank" rel="noopener">https://fonts.bunny.net/privacy</a></p>
                    
                    <h3 class="text-lg font-medium text-gray-800 mb-2">7.2 Vite (Build-Tool)</h3>
                    <p class="mb-4">Für die Entwicklung und den Build-Prozess wird Vite verwendet. Dies hat keine Auswirkungen auf die Datenerhebung bei Endnutzern, da es sich um ein reines Entwicklungs- und Build-Tool handelt.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">8. Datensicherheit</h2>
                <div class="ml-4">
                    <p>Wir setzen technische und organisatorische Sicherheitsmaßnahmen ein, um Ihre Daten gegen zufällige oder vorsätzliche Manipulationen, Verlust, Zerstörung oder Zugriff unberechtigter Personen zu schützen. Alle Passwörter werden verschlüsselt gespeichert und die Datenübertragung erfolgt über eine sichere SSL-Verbindung.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">9. Ihre Rechte</h2>
                <div class="ml-4">
                    <p class="mb-4">Sie haben folgende Rechte bezüglich Ihrer personenbezogenen Daten:</p>
                    <ul class="list-disc ml-8 pl-2 mb-4 space-y-2">
                        <li><strong>Auskunftsrecht (Art. 15 DSGVO):</strong> Sie können Auskunft über Ihre von uns verarbeiteten personenbezogenen Daten verlangen</li>
                        <li><strong>Berichtigungsrecht (Art. 16 DSGVO):</strong> Sie können die Berichtigung unrichtiger Daten verlangen</li>
                        <li><strong>Löschungsrecht (Art. 17 DSGVO):</strong> Sie können die Löschung Ihrer Daten verlangen, soweit keine gesetzlichen Aufbewahrungsfristen bestehen</li>
                        <li><strong>Einschränkungsrecht (Art. 18 DSGVO):</strong> Sie können die Einschränkung der Verarbeitung verlangen</li>
                        <li><strong>Datenübertragbarkeit (Art. 20 DSGVO):</strong> Sie können Ihre Daten in einem strukturierten Format erhalten</li>
                        <li><strong>Widerspruchsrecht (Art. 21 DSGVO):</strong> Sie können der Verarbeitung widersprechen</li>
                    </ul>
                    <p>Zur Ausübung Ihrer Rechte wenden Sie sich bitte an: <a href="mailto:niclas@thw-trainer.de" class="text-blue-600 hover:text-blue-800 underline">niclas@thw-trainer.de</a></p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">10. Beschwerderecht</h2>
                <div class="ml-4">
                    <p>Sie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über unsere Verarbeitung personenbezogener Daten zu beschweren.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">11. Weitergabe an Dritte</h2>
                <div class="ml-4">
                    <p>Eine Weitergabe Ihrer personenbezogenen Daten an Dritte findet nicht statt, es sei denn, dies ist zur Vertragsabwicklung erforderlich oder Sie haben ausdrücklich eingewilligt.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">12. Änderungen der Datenschutzerklärung</h2>
                <div class="ml-4">
                    <p>Diese Datenschutzerklärung kann bei Änderungen der Website oder bei Änderungen der rechtlichen Bestimmungen angepasst werden. Die aktuelle Version ist stets auf dieser Seite verfügbar.</p>
                </div>
            </section>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600">Stand: August 2025</p>
            </div>
        </div>
    </div>
</div>
@endsection
