@extends('layouts.landing')

@section('title', 'Impressum - THW Trainer')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8 lg:p-12">
        <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-8">Impressum</h1>

        <div class="space-y-8 text-slate-600">
            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Angaben gemäß § 5 TMG:</h2>
                <div class="ml-4">
                    <p>Niclas Reutter</p>
                    <p>Ringstraße 10</p>
                    <p>88719 Stetten</p>
                    <p>Deutschland</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Kontakt:</h2>
                <div class="ml-4">
                    <p>E-Mail: <a href="mailto:niclas@thw-trainer.de" class="text-thw-blue hover:text-blue-800 underline">niclas@thw-trainer.de</a></p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Haftung für Inhalte:</h2>
                <div class="ml-4">
                    <p>Die Inhalte dieser Webseite dienen ausschließlich zu Lern- und Informationszwecken. Trotz sorgfältiger Kontrolle übernehme ich keine Gewähr für die Aktualität, Vollständigkeit oder Richtigkeit der bereitgestellten Inhalte.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Haftung für Links:</h2>
                <div class="ml-4">
                    <p>Externe Links werden nach bestem Wissen geprüft, jedoch kann ich für deren Inhalte keine Verantwortung übernehmen. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Urheberrecht:</h2>
                <div class="ml-4">
                    <p>Die auf dieser Webseite veröffentlichten Inhalte und Werke unterliegen dem deutschen Urheberrecht. Beiträge Dritter sind als solche gekennzeichnet. Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechts bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-3">Datenschutz & Cookies:</h2>
                <div class="ml-4">
                    <p class="mb-4">Diese Webseite verwendet nur notwendige Cookies, die für den Betrieb der Seite erforderlich sind. Personenbezogene Daten wie Name, E-Mail-Adresse und Passwort werden nur im Rahmen der Anmeldung und Nutzung der Lernangebote erhoben und verarbeitet. Die Daten werden ausschließlich zur Benutzerverwaltung, Authentifizierung und zur Bereitstellung der Lerninhalte verwendet. Eine Weitergabe an Dritte findet nicht statt.</p>
                    <p>Weitere Informationen zum Datenschutz, zur Datenspeicherung und zu deinen Rechten als Nutzer findest du in der <a href="{{ route('landing.datenschutz') }}" class="text-thw-blue hover:text-blue-800 underline">Datenschutzerklärung</a>.</p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
