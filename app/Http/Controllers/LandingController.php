<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\User;
use Illuminate\View\View;

/**
 * Öffentliche Startseite und SEO-Landingpages (thw-trainer.de).
 * Alle Zahlen sind anonym und aggregiert.
 */
class LandingController extends Controller
{
    /** Regeln der Prüfungssimulation (siehe ExamController, Wiki „Die Prüfungssimulation“) */
    public const EXAM = ['questions' => 40, 'minutes' => 30, 'percent' => 80, 'passCount' => 32, 'masterAfter' => 3, 'readyAfter' => 5];

    /** Stand des Fragenkatalogs der Grundausbildung (bei neuem Katalog hier anpassen) */
    public const CATALOG = ['version' => '03.5', 'date' => 'September 2024'];

    /** Lernabschnitt => [Name, Kurzname, URL-Slug, Einleitung der Abschnittsseite] */
    public const SECTIONS = [
        1 => ['Das THW im Gefüge des Zivil- und Katastrophenschutzes', 'THW & Zivilschutz', 'thw-und-zivilschutz', 'Aufgaben, Aufbau und rechtliche Grundlagen des THW und seine Rolle im Zivil- und Katastrophenschutz.'],
        2 => ['Arbeitssicherheit und Gesundheitsschutz', 'Arbeitssicherheit', 'arbeitssicherheit', 'Unfallverhütung, persönliche Schutzausrüstung und sicheres Verhalten im Dienst und im Einsatz.'],
        3 => ['Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen', 'Leinen & Anschlagmittel', 'leinen-und-anschlagmittel', 'Knoten und Stiche, der Umgang mit Leinen, Drahtseilen und Ketten und das Anschlagen mit Rund- und Bandschlingen.'],
        4 => ['Arbeiten mit Leitern', 'Leitern', 'leitern', 'Leiterarten, sicheres Aufstellen und Besteigen von Leitern im Einsatz.'],
        5 => ['Stromerzeugung und Beleuchtung', 'Strom & Licht', 'strom-und-licht', 'Stromerzeuger, elektrische Sicherheit und das Ausleuchten von Einsatzstellen.'],
        6 => ['Metall-, Holz- und Steinbearbeitung', 'Metall, Holz, Stein', 'metall-holz-stein', 'Werkzeuge und Geräte zum Trennen, Bohren und Bearbeiten von Metall, Holz und Stein.'],
        7 => ['Bewegen von Lasten', 'Lasten bewegen', 'lasten-bewegen', 'Heben, Ziehen und Sichern von Lasten mit Hebeln, Zuggeräten und Hebern.'],
        8 => ['Arbeiten am und auf dem Wasser', 'Am und auf dem Wasser', 'am-und-auf-dem-wasser', 'Verhalten am und auf dem Wasser, Eigensicherung und Rettungsmittel.'],
        9 => ['Einsatzgrundlagen', 'Einsatzgrundlagen', 'einsatzgrundlagen', 'Ablauf von Einsätzen, Führung, Kommunikation und Verhalten an der Einsatzstelle.'],
        10 => ['Grundlagen der Rettung und Bergung', 'Rettung & Bergung', 'rettung-und-bergung', 'Retten und Bergen von Menschen, vom Erkunden der Schadensstelle bis zum Transport.'],
    ];

    public function startseite()
    {
        // In Development: eingeloggte Nutzer zum Dashboard (nur auf /, nicht auf /home)
        if (config('domains.development') && auth()->check() && ! request()->is('home')) {
            return redirect()->route('dashboard');
        }

        $sections = $this->sections();

        return view('landing.startseite', [
            'stats' => $this->stats(),
            'sections' => $sections,
            'totalQuestions' => array_sum(array_column($sections, 'count')),
            'exam' => self::EXAM,
            'demo' => $this->demoPair(),
            'faq' => $this->homeFaq(array_sum(array_column($sections, 'count'))),
            'catalog' => self::CATALOG,
        ]);
    }

    public function thwTheorie(): View
    {
        return $this->topic('thw-theorie');
    }

    public function thwGrundausbildung(): View
    {
        return $this->topic('thw-grundausbildung');
    }

    public function thwPruefungsfragen(): View
    {
        return $this->topic('thw-pruefungsfragen');
    }

    public function thwTheoriepruefung(): View
    {
        return $this->topic('thw-theoriepruefung');
    }

    private function topic(string $slug): View
    {
        $sections = $this->sections();
        $total = array_sum(array_column($sections, 'count'));

        return view('landing.topic', [
            'slug' => $slug,
            'topic' => $this->topics($sections)[$slug],
            'sections' => $sections,
            'totalQuestions' => $total,
            'exam' => self::EXAM,
            'catalog' => self::CATALOG,
            'questions' => $slug === 'thw-pruefungsfragen' ? $this->sampleQuestions() : [],
        ]);
    }

    /** Eine Seite pro Lernabschnitt mit allen veröffentlichten Fragen samt Lösung */
    public function lernabschnitt(string $abschnitt): View
    {
        $sections = $this->sections();
        $section = collect($sections)->firstWhere('slug', $abschnitt) ?? abort(404);
        $nr = $section['nr'];

        $questions = cache()->remember("landing_section_questions_{$nr}", 3600, fn () => Question::where('status', 'published')
            ->where('lernabschnitt', $nr)
            ->get(['lernabschnitt', 'nummer', 'frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesung', 'loesungsweg'])
            ->sortBy('nummer', SORT_NATURAL)
            ->map(fn ($q) => $this->format($q) + ['number' => $q->nummer])
            ->values()
            ->all());

        return view('landing.lernabschnitt', [
            'section' => $section,
            'sections' => $sections,
            'questions' => $questions,
            'totalQuestions' => array_sum(array_column($sections, 'count')),
            'catalog' => self::CATALOG,
            'prev' => $sections[$nr - 2] ?? null,
            'next' => $sections[$nr] ?? null,
        ]);
    }

    /**
     * Gecachte, anonymisierte Kennzahlen (5 Minuten). Gleicher Cache-Key wie bisher.
     */
    private function stats(): array
    {
        return cache()->remember('landing_stats_v4', 300, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            return [
                'users' => (int) (floor(User::count() / 10) * 10),
                'questions_answered' => (int) (floor($questionsAnswered / 100) * 100),
                'exams_passed' => (int) (floor($passedExams / 10) * 10),
                'pass_rate' => $totalExams > 0 ? (int) round(($passedExams / $totalExams) * 100) : 0,
            ];
        });
    }

    /** Lernabschnitte mit Fragenanzahl (1 Stunde gecacht) */
    private function sections(): array
    {
        $counts = cache()->remember('landing_section_counts', 3600, fn () => Question::selectRaw('lernabschnitt, COUNT(*) as total')
            ->groupBy('lernabschnitt')
            ->pluck('total', 'lernabschnitt')
            ->toArray());

        $sections = [];
        foreach (self::SECTIONS as $nr => [$name, $short, $slug, $intro]) {
            $sections[] = ['nr' => $nr, 'name' => $name, 'short' => $short, 'slug' => $slug, 'intro' => $intro, 'count' => (int) ($counts[$nr] ?? $counts[(string) $nr] ?? 0)];
        }

        return $sections;
    }

    /** Echte Fragen aus dem Katalog im Format der Beispielkarten */
    private function format(Question $q): array
    {
        $map = ['A' => 0, 'B' => 1, 'C' => 2];

        return [
            'section' => (int) $q->lernabschnitt,
            'text' => $q->frage,
            'answers' => [$q->antwort_a, $q->antwort_b, $q->antwort_c],
            'correct' => collect(explode(',', (string) $q->loesung))->map(fn ($l) => $map[trim($l)] ?? null)->filter(fn ($i) => $i !== null)->values()->all(),
            'explain' => $q->loesungsweg,
        ];
    }

    /** Zwei Fragen für die Scroll-Sequenz der Startseite: eine mit mehreren, eine mit einer richtigen Antwort */
    private function demoPair(): array
    {
        return cache()->remember('landing_demo_pair', 3600, function () {
            $published = fn () => Question::where('status', 'published');
            $multi = $published()->where('loesung', 'like', '%,%')->whereNotNull('loesungsweg')->inRandomOrder()->first()
                ?? $published()->where('loesung', 'like', '%,%')->inRandomOrder()->first();
            $single = $published()->where('loesung', 'not like', '%,%')->whereNotNull('loesungsweg')->inRandomOrder()->first()
                ?? $published()->where('loesung', 'not like', '%,%')->inRandomOrder()->first();

            return $multi && $single ? [$this->format($multi), $this->format($single)] : [];
        });
    }

    /** Fünf veröffentlichte Fragen zum Ausprobieren auf „THW Prüfungsfragen“, je eine aus verschiedenen Lernabschnitten */
    private function sampleQuestions(): array
    {
        return cache()->remember('landing_sample_questions', 3600, fn () => Question::where('status', 'published')
            ->inRandomOrder()
            ->get(['lernabschnitt', 'frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesung', 'loesungsweg'])
            ->unique('lernabschnitt')
            ->take(5)
            ->map(fn ($q) => $this->format($q))
            ->values()
            ->all());
    }

    private function homeFaq(int $total): array
    {
        $e = self::EXAM;
        $c = self::CATALOG;

        return [
            ['q' => 'Was kostet der THW-Trainer?', 'a' => 'Nichts. Der THW-Trainer ist komplett kostenlos, werbefrei und ohne versteckte Kosten. Er wird als private Initiative zur Unterstützung der THW-Ausbildung betrieben.'],
            ['q' => 'Welche Prüfungsfragen sind enthalten?', 'a' => "Alle {$total} Theoriefragen der THW-Grundausbildung in 10 Lernabschnitten aus dem aktuellen Fragenkatalog (Version {$c['version']}, Stand {$c['date']}) sowie Fragen verschiedener Lehrgänge."],
            ['q' => 'Kann ich auch ohne Registrierung üben?', 'a' => 'Ja. Du kannst anonym und ohne Konto Fragen üben und Prüfungssimulationen durchführen. Für Lernfortschritt, Spaced Repetition und Ortsverband-Funktionen ist eine kostenlose Registrierung nötig.'],
            ['q' => 'Wie läuft die Prüfungssimulation ab?', 'a' => "{$e['questions']} zufällig ausgewählte Fragen, {$e['minutes']} Minuten Zeit, bestanden ab {$e['percent']} Prozent – also mindestens {$e['passCount']} richtigen Antworten. Eine Frage zählt nur, wenn alle richtigen Antworten gewählt sind."],
            ['q' => 'Funktioniert die App auf dem Handy?', 'a' => 'Ja. Der THW-Trainer ist als Progressive Web App (PWA) für alle Geräte optimiert. Du kannst ihn auf dem Handy wie eine echte App installieren.'],
            ['q' => 'Ist das ein offizielles THW-Angebot?', 'a' => 'Nein. Der THW-Trainer ist eine private Initiative zur Unterstützung der THW-Ausbildung. Er wird von einem THW-Helfer ehrenamtlich entwickelt und betrieben.'],
            ['q' => 'Gibt es eine THW Prüfungsfragen App?', 'a' => 'Ja. Der THW-Trainer ist als Progressive Web App verfügbar und kann direkt auf deinem Smartphone installiert werden. Die THW Prüfungsfragen App funktioniert auf Android und iOS, ist kostenlos und benötigt keinen Download aus dem App Store. Installiere sie einfach über den Browser auf deinen Homescreen.'],
            ['q' => 'Kann ich für die THW GA Prüfung online üben?', 'a' => 'Ja. Mit dem THW-Trainer bereitest du dich gezielt auf die THW GA Prüfung (Grundausbildung) vor. GA steht für Grundausbildung – die erste und wichtigste Ausbildungsstufe im THW. Alle Prüfungsfragen stehen dir online zur Verfügung, inklusive Prüfungssimulation unter realistischen Bedingungen.'],
            ['q' => 'Wo kann ich THW Theorie Fragen online üben?', 'a' => 'Auf thw-trainer.de kannst du alle THW Theorie Fragen kostenlos online üben. Du brauchst nur einen Browser – kein Download und keine Installation. Das THW Training funktioniert auf Handy, Tablet und PC, zu Hause oder unterwegs.'],
        ];
    }

    /**
     * Inhalte der SEO-Landingpages. Jede Seite bedient eine eigene Suchintention.
     */
    private function topics(array $sections): array
    {
        $e = self::EXAM;
        $c = self::CATALOG;
        $total = array_sum(array_column($sections, 'count'));
        $simulation = "{$e['questions']} Fragen, {$e['minutes']} Minuten, bestanden ab {$e['percent']} % ({$e['passCount']} richtige)";
        $bySize = collect($sections)->sortByDesc('count')->values();
        [$big1, $big2, $small] = [$bySize->first(), $bySize->get(1), $bySize->last()];
        $bigShare = $total > 0 ? (int) round(($big1['count'] + $big2['count']) / $total * 100) : 0;

        return [
            'thw-theorie' => [
                'kind' => 'catalog',
                'title' => 'THW Theorie: Die 10 Lernabschnitte der Grundausbildung',
                'description' => "Die 10 Lernabschnitte der THW-Grundausbildung im Überblick: alle {$total} Prüfungsfragen mit Lösung, nach Thema sortiert. Fragenkatalog Version {$c['version']}, kostenlos üben.",
                'crumb' => 'Lernabschnitte',
                'eyebrow' => "THW Theorie · Fragenkatalog {$c['version']}",
                'h1' => 'Die 10 Lernabschnitte',
                'h1Accent' => "{$total} Fragen, nach Thema sortiert",
                'lead' => 'Jeder Lernabschnitt hat eine eigene Seite mit allen Fragen, Lösungen und Erklärungen. Fang mit dem an, was gerade am Dienstabend dran ist.',
                'cta' => ['label' => 'Erste Frage üben', 'route' => 'landing.guest.practice.all'],
                'blocksTitle' => 'So lernst du die Abschnitte',
                'blocks' => [
                    ['h' => 'Abschnitt für Abschnitt', 'p' => 'Die Theorie der Grundausbildung ist in 10 Lernabschnitte gegliedert, vom Auftrag des THW bis zur Rettung und Bergung. Lerne die Fragen zu dem Abschnitt, den ihr gerade am Dienstabend durchnehmt. So passen Praxis und Theorie zusammen.'],
                    ['h' => 'Die großen Abschnitte zuerst', 'p' => "Am meisten Fragen haben {$big1['short']} ({$big1['count']}) und {$big2['short']} ({$big2['count']}). Zusammen sind das {$bigShare} % des Katalogs. Plane dort mehr Lernzeit ein."],
                    ['h' => 'In der Prüfung kommt alles dran', 'p' => "In der Prüfung werden Fragen aus allen 10 Lernabschnitten gestellt. Auch die Simulation im THW-Trainer zieht aus dem ganzen Katalog: {$simulation}. Lass also keinen Abschnitt aus, auch keinen kleinen."],
                ],
                'faq' => [
                    ['q' => 'Wie viele Lernabschnitte hat die THW Grundausbildung?', 'a' => "Die Theorie der THW-Grundausbildung ist in 10 Lernabschnitte gegliedert. Zusammen enthalten sie {$total} Prüfungsfragen aus dem Fragenkatalog Version {$c['version']} (Stand {$c['date']})."],
                    ['q' => 'Welcher Lernabschnitt hat die meisten Fragen?', 'a' => "Lernabschnitt {$big1['nr']} „{$big1['name']}“ ist mit {$big1['count']} Fragen der größte. Der kleinste ist Lernabschnitt {$small['nr']} „{$small['name']}“ mit {$small['count']} Fragen."],
                    ['q' => 'Kann ich einzelne Lernabschnitte üben?', 'a' => 'Ja. Auf jeder Abschnittsseite übst du die Fragen direkt, ohne Konto. Mit einem kostenlosen Konto merkt sich der THW-Trainer deinen Fortschritt pro Lernabschnitt und wiederholt gezielt, was noch nicht sitzt.'],
                    ['q' => 'Kommen in der Prüfung alle Lernabschnitte dran?', 'a' => "Ja. Die Fragen der Theorieprüfung stammen aus allen 10 Lernabschnitten. Die Prüfungssimulation im THW-Trainer wählt ihre {$e['questions']} Fragen ebenfalls aus dem gesamten Katalog."],
                    ['q' => 'Welcher Fragenkatalog wird verwendet?', 'a' => "Der aktuelle Fragenkatalog der THW-Grundausbildung, Version {$c['version']} (Stand {$c['date']}), mit {$total} Fragen. Jede Frage hat drei Antworten, von denen eine oder mehrere richtig sind."],
                ],
                'related' => ['thw-pruefungsfragen', 'thw-theoriepruefung', 'thw-grundausbildung'],
            ],
            'thw-grundausbildung' => [
                'kind' => 'overview',
                'title' => 'THW Grundausbildung — Theorie online lernen & Prüfung bestehen 2026',
                'description' => "THW-Grundausbildung (GA): Alle {$total} Theoriefragen in 10 Lernabschnitten online lernen, mit Prüfungssimulation und Spaced Repetition. Kostenlos.",
                'crumb' => 'THW Grundausbildung',
                'eyebrow' => 'Grundausbildung (GA)',
                'h1' => 'THW Grundausbildung Theorie meistern',
                'h1Accent' => 'Vom ersten Dienstabend zur GA-Prüfung',
                'lead' => "Lerne die komplette Theorie der THW-Grundausbildung mit allen {$total} Prüfungsfragen in 10 Lernabschnitten. Verfolge deinen Fortschritt und bestehe die GA-Theorieprüfung beim ersten Versuch.",
                'cta' => ['label' => 'Kostenlos starten', 'route' => 'register'],
                'steps' => [
                    ['t' => 'Eintritt in den Ortsverband', 'd' => 'Du wirst Helferin oder Helfer in einem der rund 670 Ortsverbände und beginnst die Grundausbildung.'],
                    ['t' => 'Ausbildung an Dienstabenden', 'd' => 'Theorie und Praxis in 10 Lernabschnitten – von Leinen und Leitern bis zu Rettung und Bergung.'],
                    ['t' => 'Theorie lernen', 'd' => "{$total} Fragen. Der THW-Trainer wiederholt gezielt, was noch nicht sitzt."],
                    ['t' => 'Prüfung ablegen', 'd' => 'Theorieteil mit Fragebogen und praktischer Teil an Stationen. Danach bist du einsatzfähig.'],
                ],
                'blocksTitle' => 'Was ist die THW Grundausbildung?',
                'blocks' => [
                    ['h' => 'Die Grundausbildung im Technischen Hilfswerk', 'p' => "Die THW-Grundausbildung (kurz: GA) ist die erste Ausbildungsstufe im Technischen Hilfswerk. Jeder neue Helfer durchläuft diese Ausbildung, die aus einem praktischen und einem theoretischen Teil besteht. Die Theorie umfasst {$total} Fragen aus 10 Lernabschnitten – von Arbeitssicherheit über Einsatzgrundlagen bis hin zu technischem Wissen."],
                    ['h' => 'Der Theorieteil der Grundausbildung', 'p' => "Im theoretischen Teil der GA lernst du alle Grundlagen, die du als THW-Helfer brauchst. Der Fragenkatalog enthält {$total} Fragen zu allen 10 Lernabschnitten. Im THW-Trainer kannst du alle THW-Prüfungsfragen gezielt üben und deinen Fortschritt pro Abschnitt verfolgen."],
                    ['h' => 'Die GA-Theorieprüfung bestehen', 'p' => "Am Ende der Grundausbildung steht die Theorieprüfung: Fragen aus allen Lernabschnitten, {$e['percent']} % zum Bestehen. Mit dem THW-Trainer bereitest du dich vor – übe alle Fragen, nutze Spaced Repetition für schwierige Themen und teste dich mit der Prüfungssimulation ({$simulation})."],
                ],
                'faq' => [
                    ['q' => 'Was ist die THW Grundausbildung?', 'a' => "Die Grundausbildung (GA) ist die erste Ausbildungsstufe im THW. Sie vermittelt alle grundlegenden Fähigkeiten und Kenntnisse, die ein THW-Helfer benötigt. Die Ausbildung besteht aus einem praktischen und einem theoretischen Teil mit {$total} Prüfungsfragen."],
                    ['q' => 'Wie lange dauert die THW Grundausbildung?', 'a' => 'Die Dauer der Grundausbildung variiert je nach Ortsverband, typischerweise dauert sie mehrere Monate bei regelmäßigen Ausbildungsabenden. Die Theorie kann parallel mit dem THW-Trainer online gelernt werden.'],
                    ['q' => 'Was sind die 10 Lernabschnitte der GA?', 'a' => 'Die 10 Lernabschnitte umfassen: THW im Zivil- und Katastrophenschutz, Arbeitssicherheit, Arbeiten mit Leinen und Seilen, Leitern, Stromerzeugung, Metall-/Holz-/Steinbearbeitung, Bewegen von Lasten, Arbeiten am Wasser, Einsatzgrundlagen und Grundlagen der Rettung.'],
                    ['q' => 'Wie bestehe ich die GA-Theorieprüfung?', 'a' => "Zum Bestehen der Theorieprüfung musst du mindestens {$e['percent']} % der Prüfungsfragen korrekt beantworten. Arbeite alle Fragen im THW-Trainer durch, nutze Spaced Repetition für schwierige Fragen und teste dich mit der Prüfungssimulation."],
                    ['q' => 'Kann ich die Grundausbildung Theorie online lernen?', 'a' => "Ja. Mit dem THW-Trainer kannst du alle {$total} Theoriefragen der Grundausbildung kostenlos online lernen. Die App bietet gezieltes Lernen pro Lernabschnitt, Lernfortschritt-Tracking und eine realistische Prüfungssimulation."],
                ],
                'related' => ['thw-theorie', 'thw-theoriepruefung', 'thw-pruefungsfragen'],
            ],
            'thw-pruefungsfragen' => [
                'kind' => 'questions',
                'title' => 'THW Prüfungsfragen — Alle Fragen der Grundausbildung kostenlos üben 2026',
                'description' => "Alle {$total} THW Prüfungsfragen der Grundausbildung (GA) kostenlos online üben: mit Lösung, Erklärung und Prüfungssimulation. Probiere fünf echte Fragen direkt aus.",
                'crumb' => 'THW Prüfungsfragen',
                'eyebrow' => "Fragenkatalog Version {$c['version']}",
                'h1' => 'THW Prüfungsfragen online üben',
                'h1Accent' => 'Mit Lösung und Erklärung',
                'lead' => "Übe alle {$total} Prüfungsfragen der THW-Grundausbildung. Jede Frage hat drei Antworten, eine oder mehrere sind richtig. Probiere es direkt aus – mit fünf Fragen aus dem Katalog, ohne Anmeldung.",
                'cta' => ['label' => "Alle {$total} Fragen üben", 'route' => 'landing.guest.practice.all'],
                'blocksTitle' => 'Der THW-Fragenkatalog',
                'blocks' => [
                    ['h' => 'Alle THW Prüfungsfragen auf einen Blick', 'p' => "Der Fragenkatalog der THW-Grundausbildung umfasst {$total} Prüfungsfragen aus 10 Lernabschnitten. Jede Frage bietet drei Antwortmöglichkeiten (A, B, C), von denen eine oder mehrere richtig sein können. Im THW-Trainer findest du den kompletten Fragenkatalog zum kostenlosen Üben – übersichtlich nach Lernabschnitten gegliedert."],
                    ['h' => 'THW Prüfungsfragen online üben', 'p' => 'Mit dem THW-Trainer übst du alle Prüfungsfragen gezielt und effizient. Das Spaced-Repetition-System erkennt deine Schwächen und wiederholt schwierige Fragen automatisch häufiger. Verfolge deinen Lernfortschritt pro Lernabschnitt und sieh jederzeit, welche Themen du noch vertiefen solltest.'],
                    ['h' => 'Von der Übung zur Prüfung', 'p' => "Wenn du alle Prüfungsfragen sicher beherrschst, teste dich mit der Prüfungssimulation: {$simulation}. So gehst du optimal vorbereitet in die Prüfung."],
                ],
                'faq' => [
                    ['q' => 'Wie viele THW Prüfungsfragen gibt es?', 'a' => "Der Fragenkatalog der THW-Grundausbildung umfasst {$total} Fragen aus 10 Lernabschnitten. Die Prüfungssimulation im THW-Trainer wählt daraus {$e['questions']} Fragen zufällig aus. Im THW-Trainer kannst du alle Fragen üben."],
                    ['q' => 'Sind das die offiziellen THW Prüfungsfragen?', 'a' => "Der THW-Trainer enthält alle Prüfungsfragen der THW-Grundausbildung (GA) aus dem aktuellen Fragenkatalog, Version {$c['version']} (Stand {$c['date']}). Die Fragen werden regelmäßig auf Aktualität geprüft."],
                    ['q' => 'Kann ich die THW Prüfungsfragen kostenlos üben?', 'a' => 'Ja. Alle Prüfungsfragen sind komplett kostenlos und ohne Werbung verfügbar. Du kannst sofort anonym starten oder dich registrieren, um deinen Lernfortschritt zu speichern.'],
                    ['q' => 'Wie sind die THW Prüfungsfragen aufgebaut?', 'a' => 'Jede Frage hat drei Antwortmöglichkeiten (A, B, C). Es können eine oder mehrere Antworten richtig sein. Richtig ist eine Frage nur, wenn exakt alle richtigen Antworten gewählt sind – Teilpunkte gibt es nicht.'],
                    ['q' => 'Wie lerne ich die THW Prüfungsfragen am besten?', 'a' => 'Arbeite alle Fragen systematisch durch, nutze die Spaced-Repetition-Funktion für schwierige Fragen und teste dich regelmäßig mit der Prüfungssimulation. So gehst du optimal vorbereitet in die THW-Theorieprüfung.'],
                ],
                'related' => ['thw-theorie', 'thw-theoriepruefung', 'thw-grundausbildung'],
            ],
            'thw-theoriepruefung' => [
                'kind' => 'exam',
                'title' => 'THW Theorieprüfung — Online Prüfungssimulation kostenlos 2026',
                'description' => "THW Theorieprüfung online simulieren: {$e['questions']} Fragen, {$e['minutes']} Minuten, Bestehen ab {$e['percent']} %. Kostenlose Vorbereitung auf die THW-Grundausbildung, beliebig oft wiederholbar.",
                'crumb' => 'THW Theorieprüfung',
                'eyebrow' => 'Prüfungssimulation',
                'h1' => 'THW Theorieprüfung online bestehen',
                'h1Accent' => 'So läuft sie ab, so bestehst du',
                'lead' => "Simuliere die THW-Theorieprüfung unter realistischen Bedingungen: {$simulation}. Danach siehst du genau, wo du noch nachlegen musst. Kostenlos und beliebig oft wiederholbar.",
                'cta' => ['label' => 'Simulation starten', 'route' => 'landing.guest.exam.index'],
                'tips' => [
                    ['t' => 'Alle Antworten lesen', 'd' => 'Mehrere Antworten können richtig sein. Achte besonders auf Fragen mit Mehrfachantworten – hier liegen die häufigsten Fehler.'],
                    ['t' => 'Zeit einteilen', 'd' => "{$e['minutes']} Minuten für {$e['questions']} Fragen sind rund 45 Sekunden pro Frage. Unsichere Fragen markieren und später zurückkehren."],
                    ['t' => 'Fehler gezielt wiederholen', 'd' => 'Nach jeder Simulation landen falsch beantwortete Fragen in deiner Wiederholungsliste.'],
                    ['t' => "{$e['readyAfter']}-mal bestehen", 'd' => "Wer die Simulation {$e['readyAfter']}-mal bestanden hat, gilt im THW-Trainer als prüfungsbereit."],
                ],
                'blocksTitle' => 'So läuft die THW Theorieprüfung ab',
                'blocks' => [
                    ['h' => 'Ablauf der THW Theorieprüfung', 'p' => "In der Theorieprüfung der THW-Grundausbildung werden Fragen zufällig aus dem Fragenkatalog mit {$total} Fragen ausgewählt. Jede Frage bietet drei Antwortmöglichkeiten (A, B, C), wobei eine oder mehrere Antworten richtig sein können. Zum Bestehen sind mindestens {$e['percent']} % korrekte Antworten erforderlich – in der Simulation mit {$e['questions']} Fragen also mindestens {$e['passCount']}."],
                    ['h' => 'Prüfungsvorbereitung mit dem THW-Trainer', 'p' => 'Übe alle THW-Prüfungsfragen systematisch und nutze das Spaced-Repetition-System, das deine Schwachstellen erkennt und schwierige Fragen automatisch häufiger wiederholt. Mit der Prüfungssimulation testest du dich unter realistischen Bedingungen – mit Zeitlimit und sofortiger Auswertung.'],
                    ['h' => 'Tipps zum Bestehen der Theorieprüfung', 'p' => 'Arbeite alle Prüfungsfragen mindestens einmal durch, um dir einen Überblick zu verschaffen. Konzentriere dich dann auf die Lernabschnitte, in denen du noch unsicher bist. Nutze die Prüfungssimulation regelmäßig, um das Prüfungsformat zu verinnerlichen.'],
                ],
                'faq' => [
                    ['q' => 'Wie läuft die THW Theorieprüfung ab?', 'a' => "Es werden Fragen aus dem Fragenkatalog mit {$total} Fragen gestellt. Jede Frage hat drei Antwortmöglichkeiten (A, B, C), eine oder mehrere können richtig sein. Zum Bestehen sind mindestens {$e['percent']} % korrekte Antworten erforderlich."],
                    ['q' => 'Wie viele Fragen muss ich richtig beantworten?', 'a' => "Mindestens {$e['percent']} %. In der Prüfungssimulation des THW-Trainers mit {$e['questions']} Fragen sind das {$e['passCount']} richtige Antworten. Die Fragen werden zufällig aus allen 10 Lernabschnitten ausgewählt."],
                    ['q' => 'Kann ich die THW Theorieprüfung online simulieren?', 'a' => "Ja. Im THW-Trainer simulierst du die Prüfung unter realistischen Bedingungen: {$simulation}, mit sofortiger Auswertung. Die Simulation ist kostenlos und beliebig oft wiederholbar."],
                    ['q' => 'Was passiert, wenn ich die THW Prüfung nicht bestehe?', 'a' => 'Du kannst die Theorieprüfung wiederholen. Nutze den THW-Trainer, um gezielt deine Schwachstellen zu üben – falsch beantwortete Fragen landen automatisch in deiner Wiederholung.'],
                    ['q' => 'Wie kann ich mich auf die THW Theorieprüfung vorbereiten?', 'a' => 'Alle Prüfungsfragen im THW-Trainer systematisch durcharbeiten, schwierige Fragen mit Spaced Repetition wiederholen und regelmäßig die Prüfungssimulation nutzen. So kennst du das Prüfungsformat und bist optimal vorbereitet.'],
                ],
                'related' => ['thw-pruefungsfragen', 'thw-theorie', 'thw-grundausbildung'],
            ],
        ];
    }
}
