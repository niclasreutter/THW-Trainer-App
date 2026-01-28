{{-- Landing Page Footer - THW Blue --}}
<footer class="landing-footer">
    <div class="landing-footer-container">
        {{-- Logo und Beschreibung --}}
        <div class="landing-footer-brand">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer" class="h-8 w-auto">
                <span class="font-bold text-xl text-white">THW-Trainer</span>
            </div>
            <p class="text-blue-100 text-sm max-w-xs">
                Kostenlose Prüfungsvorbereitung für die THW Grundausbildung. Lerne effektiv mit allen Theoriefragen.
            </p>
        </div>

        {{-- Links --}}
        <div class="landing-footer-links-section">
            <h4 class="text-white font-semibold mb-4">Links</h4>
            <div class="landing-footer-links">
                <a href="{{ route('landing.home') }}">Startseite</a>
                <a href="{{ route('landing.statistics') }}">Statistiken</a>
                <a href="{{ route('landing.contact.index') }}">Kontakt</a>
            </div>
        </div>

        {{-- Rechtliches --}}
        <div class="landing-footer-legal-section">
            <h4 class="text-white font-semibold mb-4">Rechtliches</h4>
            <div class="landing-footer-links">
                <a href="{{ route('landing.impressum') }}">Impressum</a>
                <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            </div>
        </div>

        {{-- Unterstützung --}}
        <div class="landing-footer-support">
            <h4 class="text-white font-semibold mb-4">Unterstützen</h4>
            <p class="text-blue-100 text-sm mb-3">
                THW-Trainer ist kostenlos. Unterstütze die Entwicklung mit einem Kaffee!
            </p>
            <a href="https://paypal.me/reuttern" target="_blank" rel="noopener noreferrer" class="landing-footer-support-btn">
                <i class="bi bi-cup-hot mr-2"></i>
                Kaffee spendieren
            </a>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="landing-footer-copyright">
        <div class="landing-footer-copyright-container">
            <p>&copy; {{ date('Y') }} THW-Trainer. Entwickelt von Niclas Reutter.</p>
            <p class="text-blue-200 text-xs mt-1">
                Kein offizielles Angebot des THW. Private Initiative zur Prüfungsvorbereitung.
            </p>
        </div>
    </div>
</footer>
