@extends('layouts.app')

@section('title', 'THW Theorie kostenlos lernen 2026 | Alle Prüfungsfragen + Lernen im Ortsverband')
@section('description', 'THW Theorie: alle aktuelle Prüfungsfragen ✓ Grundausbildung & Lehrgänge ✓ Eigene Fragen erstellen ✓ Ortsverband-Lernpools ✓ Kostenlos & werbefrei')

@section('content')
<div class="overflow-x-hidden">

    <!-- Account gelöscht Meldung  -->
    @if (session('status') == 'account-deleted')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 bg-thw-blue">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            ✅ Dein Account wurde erfolgreich gelöscht. Alle deine Daten wurden permanent entfernt.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Hero Section mit Gradient -->
    <section class="relative h-[50vh]" style="background: linear-gradient(to bottom, #00337F 0%, rgba(0, 51, 127, 0.8) 50%, transparent 100%);" aria-label="Hauptbereich">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center px-4 max-w-[90%] w-full">
                <!-- Haupttitel mit strukturiertem H1 -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-4 tracking-tight" style="color: white;">
                    <span style="display: inline-block; background: linear-gradient(90deg, #fbbf24, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">THW Theorie lernen</span>
                    <br class="hidden sm:block">
                    <span class="text-2xl sm:text-3xl lg:text-4xl font-light text-blue-100">Kostenlose Prüfungsvorbereitung 2026</span>
                </h1>

                <!-- Untertitel -->
                <p class="text-lg sm:text-xl lg:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto font-light leading-relaxed">
                    Dein digitaler Begleiter für die THW Grundausbildung - Jetzt kostenlos starten!
                </p>

                <!-- CTA Button -->
                <a href="#"
                   id="cta-button"
                   class="inline-flex items-center justify-center px-6 py-3 lg:px-8 lg:py-4 text-base lg:text-lg font-bold rounded-xl shadow-lg hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1e3a8a;"
                   onclick="launchRocket(event)"
                   aria-label="Jetzt kostenlos THW-Theorie lernen starten">
                    <i id="rocket" class="bi bi-check2-circle text-xl lg:text-2xl mr-2" aria-hidden="true"></i>
                    <span>THW-Theorie kostenlos lernen</span>
                </a>

                <!-- Anonym Text -->
                <div class="mt-4">
                    <a href="{{ route('guest.practice.menu') }}"
                       class="text-sm lg:text-base text-blue-900 underline font-medium hover:text-yellow-400 transition-colors duration-300"
                       aria-label="Anonym ohne Registrierung üben">
                        Anonym ohne Login üben
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 lg:py-20 bg-gray-50" aria-labelledby="features-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <header class="text-center mb-12 lg:mb-16">
                <h2 id="features-heading" class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 lg:mb-6 tracking-tight">
                    Was bietet der THW-Trainer?
                </h2>
                <p class="text-base lg:text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed font-light">
                    Alles für deine Grundausbildung Theorie-Prüfung im THW
                </p>
            </header>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Feature 1: Alle Theoriefragen -->
                <article class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div aria-hidden="true" class="text-4xl mb-3 text-thw-blue"><i class="bi bi-book"></i></div>
                    <h3 class="text-xl font-semibold text-thw-blue mb-3">Alle Theoriefragen</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Umfassende Sammlung aller <strong>THW-Theoriefragen</strong> zum Üben und Lernen.
                        Von Grundlagen bis zu spezialisierten Bereichen.
                    </p>
                </article>

                <!-- Feature 2: Prüfungssimulation -->
                <article class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div aria-hidden="true" class="text-4xl mb-3 text-thw-blue"><i class="bi bi-mortarboard"></i></div>
                    <h3 class="text-xl font-semibold text-thw-blue mb-3">Prüfungssimulation</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Realistische <strong>Prüfungssimulation</strong> verfügbar, sobald alle Fragen bearbeitet wurden.
                        Teste dich unter echten Bedingungen.
                    </p>
                </article>

                <!-- Feature 3: Lernfortschritt -->
                <article class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div aria-hidden="true" class="text-4xl mb-3 text-thw-blue"><i class="bi bi-bar-chart"></i></div>
                    <h3 class="text-xl font-semibold text-thw-blue mb-3">Lernfortschritt tracken</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Dein Fortschritt wird gespeichert und im persönlichen Dashboard angezeigt.
                        Verfolge deine Erfolge und Schwächen.
                    </p>
                </article>

                <!-- Feature 4: PWA -->
                <article class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div aria-hidden="true" class="text-4xl mb-3 text-thw-blue"><i class="bi bi-phone"></i></div>
                    <h3 class="text-xl font-semibold text-thw-blue mb-3">Als App installierbar</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Installiere THW Trainer als <strong>Progressive Web App</strong> auf deinem Smartphone für schnelleren Zugriff vom Homescreen.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 lg:py-16 bg-thw-blue" aria-labelledby="cta-heading">
        <div class="max-w-6xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 id="cta-heading" class="text-2xl lg:text-4xl font-bold text-white mb-4 lg:mb-5 tracking-tight">
                Bereit zum Lernen?
            </h2>
            <p class="text-base lg:text-lg text-white mb-4 lg:mb-6 max-w-4xl mx-auto leading-relaxed font-light">
                Starte jetzt mit dem THW-Trainer und bereite dich optimal auf deine <strong class="font-semibold">Grundausbildung Theorie-Prüfung im THW</strong> vor.
            </p>
            <p class="text-sm lg:text-base text-white max-w-4xl mx-auto leading-relaxed font-light mb-8">
                Registriere dich kostenlos und beginne sofort mit dem Lernen, egal ob Handy, Laptop oder Tablet!<br>
                <strong class="font-semibold">Ein Account, ein Lernstand!</strong>
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                <a href="{{ route('register') }}"
                   class="inline-block bg-yellow-400 text-blue-900 px-8 py-4 rounded-xl font-bold hover:bg-yellow-300 hover:scale-105 transition-all duration-300 text-center shadow-lg"
                   aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>

                <a href="{{ route('login') }}"
                   class="inline-block bg-white text-blue-900 px-8 py-4 rounded-xl font-bold border-2 border-white hover:bg-gray-100 hover:scale-105 transition-all duration-300 text-center shadow-lg"
                   aria-label="Zum Login">
                    Login
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-12 lg:py-20 bg-gray-50" aria-labelledby="faq-heading">
        <div class="max-w-2xl lg:max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-8 lg:mb-12">
                <h2 id="faq-heading" class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                    Häufig gestellte Fragen
                </h2>
                <p class="text-base lg:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Alles über <strong>THW Grundausbildung</strong> und den THW-Trainer
                </p>
            </header>
            
            <div class="space-y-4" itemscope itemtype="https://schema.org/FAQPage">
                <!-- FAQ Item 1 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq1')" aria-expanded="false" aria-controls="faq1">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Was ist die THW Grundausbildung?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq1" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Die <strong>THW Grundausbildung</strong> ist die erste Ausbildungsstufe im <strong>Technischen Hilfswerk</strong>.
                            Sie vermittelt die grundlegenden Kenntnisse und Fähigkeiten für alle THW-Helfer.
                            Die <strong>Theorie-Prüfung</strong> ist ein wichtiger Bestandteil dieser Ausbildung und umfasst
                            Themen wie Rechtsgrundlagen, Organisation des THW, Einsatzgrundlagen und technisches Wissen.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 2 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq2')" aria-expanded="false" aria-controls="faq2">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Wie bereite ich mich auf die THW Grundausbildung Theorie vor?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq2" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der <strong>THW-Trainer</strong> bietet dir alle aktuellen <strong>THW-Theoriefragen</strong> zur optimalen Vorbereitung.
                            Übe systematisch alle Themenbereiche, nutze die <strong>Prüfungssimulation</strong> und verfolge deinen Lernfortschritt.
                            Die App funktioniert auf allen Geräten, sodass du auch unterwegs lernen kannst.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 3 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq3')" aria-expanded="false" aria-controls="faq3">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Ist der THW-Trainer kostenlos?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq3" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Ja, der <strong>THW-Trainer ist komplett kostenlos</strong>! Du kannst sofort mit dem Lernen beginnen,
                            ohne jegliche Kosten. Auch eine Anmeldung ist nicht zwingend erforderlich -
                            du kannst anonym üben oder dich kostenlos registrieren, um deinen Lernfortschritt zu speichern.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 4 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq4')" aria-expanded="false" aria-controls="faq4">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Wie viele Fragen gibt es im THW-Trainer?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq4" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der THW-Trainer enthält <strong>alle aktuellen THW-Theoriefragen</strong> aus allen relevanten Bereichen
                            der Grundausbildung. Die Fragen werden regelmäßig aktualisiert und spiegeln den
                            aktuellen Stand der THW-Ausbildung wider.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 5 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq5')" aria-expanded="false" aria-controls="faq5">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Welche Themen werden in der THW Grundausbildung abgefragt?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq5" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Die <strong>THW Grundausbildung</strong> umfasst Themen wie: <strong>Rechtsgrundlagen</strong>, Organisation des THW,
                            Einsatzgrundlagen, Gefahren der Einsatzstelle, Technische Hilfe, Einsatzablauf,
                            Führung und Kommunikation.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 6 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq6')" aria-expanded="false" aria-controls="faq6">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Funktioniert der THW-Trainer auf dem Handy?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq6" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Ja, der THW-Trainer ist <strong>vollständig responsive</strong> und funktioniert optimal auf Smartphones,
                            Tablets und Desktop-Computern. Du kannst überall und jederzeit lernen -
                            egal ob zu Hause, unterwegs oder in der Pause.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 7 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq7')" aria-expanded="false" aria-controls="faq7">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Wie schwer ist die THW Grundausbildung Theorie-Prüfung?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq7" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Mit der richtigen Vorbereitung ist die <strong>THW Grundausbildung Theorie-Prüfung</strong> gut zu schaffen.
                            Der THW-Trainer hilft dir dabei, alle wichtigen Themen zu verstehen und zu üben.
                            Nutze die <strong>Prüfungssimulation</strong>, um dich unter realistischen Bedingungen zu testen.
                        </p>
                    </div>
                </article>

                <!-- FAQ Item 8 -->
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-toggle w-full text-left p-5 lg:p-6 flex justify-between items-center hover:bg-blue-50 transition-colors" onclick="toggleFAQ('faq8')" aria-expanded="false" aria-controls="faq8">
                        <span class="text-base lg:text-lg font-semibold text-gray-900 pr-4" itemprop="name">Ist der THW-Trainer offiziell vom THW?</span>
                        <span class="faq-icon text-2xl text-thw-blue font-bold flex-shrink-0" aria-hidden="true">+</span>
                    </button>
                    <div id="faq8" class="faq-content hidden px-5 lg:px-6 pb-5 lg:pb-6" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der THW-Trainer ist eine <strong>private Initiative</strong> eines aktiven THW-Mitglieds und nicht offiziell
                            vom THW herausgegeben. Die Fragen basieren jedoch auf den offiziellen Ausbildungsunterlagen
                            und werden regelmäßig aktualisiert, um den aktuellen Stand der THW-Ausbildung zu reflektieren.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Der Kopf Dahinter Section -->
    <section class="py-12 lg:py-20 bg-white" aria-labelledby="about-heading">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-16 items-center">
                <!-- Bild Container -->
                <div class="flex-shrink-0 flex justify-center lg:justify-start">
                    <picture>
                        <source srcset="{{ asset('niclas_compressed.webp') }}" type="image/webp">
                        <img src="{{ asset('niclas_compressed.png') }}"
                             alt="Niclas Reutter - Entwickler und aktives THW-Mitglied, Entwickler des THW-Trainers"
                             class="rounded-2xl shadow-xl"
                             style="max-height: 300px; max-width: 250px;"
                             loading="lazy"
                             width="250"
                             height="300">
                    </picture>
                </div>

                <!-- Text Container -->
                <div class="flex-1 space-y-6" itemscope itemtype="https://schema.org/Person">
                    <h2 id="about-heading" class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 tracking-tight">
                        Der Kopf Dahinter
                    </h2>
                    <div class="space-y-4 text-base lg:text-lg text-gray-700 leading-relaxed">
                        <p>
                            Hallo! Ich bin <span itemprop="name">Niclas</span>, der <span itemprop="jobTitle">Entwickler</span> hinter dem <strong>THW-Trainer</strong>. Als aktives <strong>THW-Mitglied</strong>
                            kenne ich die Herausforderungen bei der Vorbereitung auf die Theoriefragen nur zu gut.
                        </p>
                        <p>
                            Mit dieser App möchte ich dir eine moderne, intuitive und effektive Möglichkeit bieten,
                            dich optimal auf deine <strong>THW-Prüfung</strong> vorzubereiten. Alle Fragen sind sorgfältig ausgewählt
                            und spiegeln den aktuellen Stand der <strong>THW-Ausbildung</strong> wider.
                        </p>
                        <p class="font-semibold">
                            Viel Erfolg bei deiner Prüfung!
                        </p>
                        <p>
                            Diese Webseite stelle ich <strong>kostenlos zur Verfügung</strong> und finanziere alle Kosten für Webseite, Domain und Server selbst.
                            Unterstütze mich mit einem Kaffee!
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="https://paypal.me/reuttern"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center px-8 py-4 bg-thw-blue text-white font-bold rounded-xl hover:bg-blue-800 hover:scale-105 transition-all duration-300 shadow-lg"
                           aria-label="Unterstütze den Entwickler mit einer Kaffee-Spende via PayPal">
                            <i class="bi bi-cup-hot mr-2 text-xl" aria-hidden="true"></i>
                            Unterstütze mich
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
/* Custom THW Blue Color */
.bg-thw-blue {
    background-color: #00337F;
}

.text-thw-blue {
    color: #00337F;
}

/* FAQ Smooth Transitions */
.faq-content {
    transition: max-height 0.3s ease-out;
}

.faq-icon {
    transition: transform 0.3s ease;
}
</style>

<script>
/**
 * CTA Button Weiterleitung
 */
function launchRocket(event) {
    event.preventDefault();
    window.location.href = '{{ route("dashboard") }}';
}

/**
 * FAQ Toggle mit Accessibility Support
 */
function toggleFAQ(faqId) {
    const content = document.getElementById(faqId);
    const button = content?.previousElementSibling;
    const icon = button?.querySelector('.faq-icon');

    if (!content || !button || !icon) return;

    const isHidden = content.classList.contains('hidden');

    if (isHidden) {
        // FAQ öffnen
        content.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
        icon.textContent = '−';
    } else {
        // FAQ schließen
        content.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
        icon.textContent = '+';
    }
}

/**
 * Initialize FAQ Accessibility
 */
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Keyboard Navigation
    const faqButtons = document.querySelectorAll('.faq-toggle');
    faqButtons.forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    });

    // Preload Dashboard Route für schnelleren Seitenwechsel
    const dashboardLink = document.createElement('link');
    dashboardLink.rel = 'prefetch';
    dashboardLink.href = '{{ route("dashboard") }}';
    document.head.appendChild(dashboardLink);
});
</script>

<!-- Schema.org Structured Data für Rich Snippets -->

<!-- FAQPage Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Was ist die THW Grundausbildung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die THW Grundausbildung ist die erste Ausbildungsstufe im Technischen Hilfswerk. Sie vermittelt die grundlegenden Kenntnisse und Fähigkeiten für alle THW-Helfer. Die Theorie-Prüfung ist ein wichtiger Bestandteil dieser Ausbildung und umfasst Themen wie Rechtsgrundlagen, Organisation des THW, Einsatzgrundlagen und technisches Wissen."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie bereite ich mich auf die THW Grundausbildung Theorie vor?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Der THW-Trainer bietet dir alle aktuellen THW-Theoriefragen zur optimalen Vorbereitung. Übe systematisch alle Themenbereiche, nutze die Prüfungssimulation und verfolge deinen Lernfortschritt. Die App funktioniert auf allen Geräten, sodass du auch unterwegs lernen kannst."
            }
        },
        {
            "@@type": "Question",
            "name": "Ist der THW-Trainer kostenlos?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, der THW-Trainer ist komplett kostenlos! Du kannst sofort mit dem Lernen beginnen, ohne jegliche Kosten. Auch eine Anmeldung ist nicht zwingend erforderlich - du kannst anonym üben oder dich kostenlos registrieren, um deinen Lernfortschritt zu speichern."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie viele Fragen gibt es im THW-Trainer?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Der THW-Trainer enthält alle aktuellen THW-Theoriefragen aus allen relevanten Bereichen der Grundausbildung. Die Fragen werden regelmäßig aktualisiert und spiegeln den aktuellen Stand der THW-Ausbildung wider."
            }
        },
        {
            "@@type": "Question",
            "name": "Welche Themen werden in der THW Grundausbildung abgefragt?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die THW Grundausbildung umfasst Themen wie: Rechtsgrundlagen, Organisation des THW, Einsatzgrundlagen, Gefahren der Einsatzstelle, Technische Hilfe, Einsatzablauf, Führung und Kommunikation."
            }
        },
        {
            "@@type": "Question",
            "name": "Funktioniert der THW-Trainer auf dem Handy?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, der THW-Trainer ist vollständig responsive und funktioniert optimal auf Smartphones, Tablets und Desktop-Computern. Du kannst überall und jederzeit lernen - egal ob zu Hause, unterwegs oder in der Pause."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie schwer ist die THW Grundausbildung Theorie-Prüfung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Mit der richtigen Vorbereitung ist die THW Grundausbildung Theorie-Prüfung gut zu schaffen. Der THW-Trainer hilft dir dabei, alle wichtigen Themen zu verstehen und zu üben. Nutze die Prüfungssimulation, um dich unter realistischen Bedingungen zu testen."
            }
        },
        {
            "@@type": "Question",
            "name": "Ist der THW-Trainer offiziell vom THW?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Der THW-Trainer ist eine private Initiative eines aktiven THW-Mitglieds und nicht offiziell vom THW herausgegeben. Die Fragen basieren jedoch auf den offiziellen Ausbildungsunterlagen und werden regelmäßig aktualisiert, um den aktuellen Stand der THW-Ausbildung zu reflektieren."
            }
        }
    ]
}
</script>

<!-- WebApplication Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "THW-Trainer",
    "url": "{{ url('/') }}",
    "description": "Kostenlose THW Theorie Lernplattform für Grundausbildung, FüUF26 und mehr. Lernen im Ortsverband mit eigenen Fragen und Prüfungssimulation.",
    "applicationCategory": "EducationalApplication",
    "operatingSystem": "Web Browser, iOS, Android",
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR",
        "availability": "https://schema.org/InStock"
    },
    "author": {
        "@@type": "Person",
        "name": "Niclas Reutter",
        "url": "https://niclas-reutter.de"
    },
    "featureList": [
        "THW Grundausbildungsfragen",
        "Lernen für FüUF26",
        "Lernen im Ortsverband",
        "Eigene Fragen erstellen",
        "Prüfungssimulation",
        "Lernfortschritt Tracking",
        "Progressive Web App",
        "Kostenlos und werbefrei"
    ],
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "4.7",
        "bestRating": "5",
        "worstRating": "1",
        "ratingCount": "87"
    }
}
</script>

<!-- Organization Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "THW-Trainer",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('logo-thwtrainer.png') }}",
    "sameAs": [
        "https://github.com/niclasreutter"
    ],
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "Support",
        "email": "support@thw-trainer.de",
        "availableLanguage": "de"
    }
}
</script>

<!-- BreadcrumbList Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ url('/') }}"
        }
    ]
}
</script>

@endsection
