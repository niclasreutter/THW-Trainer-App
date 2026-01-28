<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

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

        // Hauptseite (höchste Priorität)
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily');

        // Öffentliche Gast-Bereiche (wichtig für SEO!)
        $sitemap .= $this->addUrl(route('guest.practice.menu'), '0.9', 'weekly');
        $sitemap .= $this->addUrl(route('guest.practice.menu'), '0.8', 'weekly'); // Gast Übung

        // Auth Seiten
        $sitemap .= $this->addUrl(route('register'), '0.8', 'monthly');
        $sitemap .= $this->addUrl(route('login'), '0.7', 'monthly');

        // Dashboard (für eingeloggte User)
        $sitemap .= $this->addUrl(route('dashboard'), '0.6', 'weekly');

        // Rechtliches
        $sitemap .= $this->addUrl(route('landing.impressum'), '0.3', 'yearly');
        $sitemap .= $this->addUrl(route('landing.datenschutz'), '0.3', 'yearly');

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Helper function to add URL to sitemap
     *
     * @param string $url
     * @param string $priority
     * @param string $changefreq
     * @return string
     */
    private function addUrl(string $url, string $priority, string $changefreq): string
    {
        $xml = '    <url>' . PHP_EOL;
        $xml .= '        <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
        $xml .= '        <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
        $xml .= '        <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '        <priority>' . $priority . '</priority>' . PHP_EOL;
        $xml .= '    </url>' . PHP_EOL . PHP_EOL;

        return $xml;
    }
}
