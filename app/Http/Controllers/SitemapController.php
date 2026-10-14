<?php

namespace App\Http\Controllers;

use App\Helpers\DomainHelper;
use App\Models\Question;
use App\Services\WikiService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml dynamically
     *
     * @return Response
     */
    public function index(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $sitemap .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $sitemap .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . PHP_EOL;
        $sitemap .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL . PHP_EOL;

        $landing = [app_path('Http/Controllers/LandingController.php'), resource_path('views/layouts/public.blade.php')];
        $view = fn (string $name) => resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');

        // Hauptseite (höchste Priorität - DAS ist die wichtigste Seite!), gleiche URL wie im Canonical
        $sitemap .= $this->addUrl(DomainHelper::landingUrl('/'), '1.0', 'daily', $this->lastmod([...$landing, $view('landing.startseite')]));

        // SEO Sub-Landingpages
        foreach (['thw-theorie' => '0.9', 'thw-pruefungsfragen' => '0.8', 'thw-theoriepruefung' => '0.8', 'thw-grundausbildung' => '0.8'] as $slug => $priority) {
            $sitemap .= $this->addUrl(route('landing.' . $slug), $priority, 'weekly', $this->lastmod([...$landing, $view('landing.topic')]));
        }

        // Eine Seite pro Lernabschnitt: geändert, wenn sich Vorlage oder eine Frage des Abschnitts ändert
        $questionsUpdated = Question::selectRaw('lernabschnitt, MAX(updated_at) as updated')->groupBy('lernabschnitt')->pluck('updated', 'lernabschnitt');
        foreach (LandingController::SECTIONS as $nr => [, , $slug]) {
            $updated = strtotime((string) ($questionsUpdated[$nr] ?? '')) ?: 0;
            $sitemap .= $this->addUrl(route('landing.thw-theorie.abschnitt', $slug), '0.7', 'weekly', $this->lastmod([...$landing, $view('landing.lernabschnitt')], $updated));
        }

        // Öffentliche Statistik-Seite (zeigt tagesaktuelle Zahlen)
        $sitemap .= $this->addUrl(route('landing.statistics'), '0.7', 'weekly', date('Y-m-d'));

        // Wiki / Anleitung (alle Seiten aus config/wiki.php)
        $wiki = app(WikiService::class);
        foreach ($wiki->allPages() as $page) {
            $sitemap .= $this->addUrl($page['url'], '0.6', 'weekly', $this->lastmod([resource_path('views/layouts/public.blade.php'), $view('wiki.show'), $wiki->resolvePath($page['file'])]));
        }

        // Gast-Übungsmodus (niedrigere Priorität als Hauptseite!)
        $sitemap .= $this->addUrl(url('/guest/practice-menu'), '0.5', 'monthly', $this->lastmod([...$landing, $view('landing.guest.practice-menu')]));

        // Login/Registrierung liegen auf der App-Domain (robots.txt: Disallow) und gehören nicht in die Sitemap

        // Rechtliches
        $sitemap .= $this->addUrl(route('landing.impressum'), '0.3', 'yearly', $this->lastmod([$view('landing.impressum')]));
        $sitemap .= $this->addUrl(route('landing.datenschutz'), '0.3', 'yearly', $this->lastmod([$view('landing.datenschutz')]));

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Jüngste Änderung der Dateien, aus denen eine Seite entsteht.
     * ponytail: Dateizeitstempel statt Inhalts-Hash. Schreibt ein Deployment alle Dateien neu, zeigt lastmod das Deploy-Datum.
     */
    private function lastmod(array $files, int $minTimestamp = 0): string
    {
        $times = array_map(fn ($file) => is_file($file) ? filemtime($file) : 0, $files);

        return date('Y-m-d', max($minTimestamp, ...$times) ?: time());
    }

    /**
     * Helper function to add URL to sitemap
     */
    private function addUrl(string $url, string $priority, string $changefreq, string $lastmod): string
    {
        $xml = '    <url>' . PHP_EOL;
        $xml .= '        <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
        $xml .= '        <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '        <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '        <priority>' . $priority . '</priority>' . PHP_EOL;
        $xml .= '    </url>' . PHP_EOL . PHP_EOL;

        return $xml;
    }
}
