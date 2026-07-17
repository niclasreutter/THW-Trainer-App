<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Öffentliches Wiki: rendert Markdown-Dateien aus resources/wiki/
 * (bzw. Projekt-Root via 'root:'-Präfix, z.B. CHANGELOG.md).
 *
 * - Überschriften (h2-h4) bekommen IDs + Anker-Links (Deep-Links wie bei Microsoft Learn)
 * - Inhaltsverzeichnis ("In diesem Artikel") wird aus h2/h3 extrahiert
 * - GitHub-Callouts ([!NOTE], [!TIP], ...) werden zu gestylten Hinweisboxen
 * - Ergebnis wird gecacht, Cache-Key enthält die Datei-mtime (auto-invalidierend)
 */
class WikiService
{
    /**
     * Navigation: Sektionen mit Seiten inkl. URL und Aktiv-Slug.
     */
    public function navigation(): array
    {
        $sections = [];

        foreach (config('wiki.sections', []) as $section) {
            $pages = [];
            foreach ($section['pages'] as $slug => $page) {
                $pages[] = [
                    'slug' => $slug,
                    'title' => $page['title'],
                    'url' => $this->url($slug),
                ];
            }

            $sections[] = [
                'title' => $section['title'],
                'icon' => $section['icon'] ?? 'bi-folder',
                'pages' => $pages,
            ];
        }

        return $sections;
    }

    /**
     * Alle Seiten flach in Navigationsreihenfolge (für Prev/Next & Sitemap).
     *
     * @return array<string, array> slug => meta
     */
    public function allPages(): array
    {
        $pages = [];

        foreach (config('wiki.sections', []) as $section) {
            foreach ($section['pages'] as $slug => $page) {
                $pages[$slug] = $page + [
                    'slug' => $slug,
                    'section' => $section['title'],
                    'url' => $this->url($slug),
                ];
            }
        }

        return $pages;
    }

    public function find(string $slug): ?array
    {
        return $this->allPages()[$slug] ?? null;
    }

    public function url(string $slug): string
    {
        return $slug === 'index'
            ? route('landing.wiki.index')
            : route('landing.wiki.show', $slug);
    }

    /**
     * Seite rendern: HTML + Inhaltsverzeichnis + Vor/Zurück-Navigation.
     */
    public function render(string $slug): ?array
    {
        $meta = $this->find($slug);
        if ($meta === null) {
            return null;
        }

        $path = $this->resolvePath($meta['file']);
        if (! is_file($path)) {
            return null;
        }

        $mtime = filemtime($path);

        $rendered = Cache::remember(
            "wiki:page:{$slug}:{$mtime}",
            now()->addDay(),
            function () use ($path) {
                $html = $this->convert(file_get_contents($path));
                $html = $this->transformCallouts($html);

                return [
                    'html' => $html,
                    'toc' => $this->extractToc($html),
                ];
            }
        );

        [$prev, $next] = $this->neighbours($slug);

        return [
            'slug' => $slug,
            'meta' => $meta,
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
            'prev' => $prev,
            'next' => $next,
            'updated_at' => Carbon::createFromTimestamp($mtime),
        ];
    }

    private function resolvePath(string $file): string
    {
        if (str_starts_with($file, 'root:')) {
            return base_path(substr($file, 5));
        }

        return resource_path('wiki/' . $file);
    }

    private function neighbours(string $slug): array
    {
        $pages = array_values($this->allPages());
        $index = null;

        foreach ($pages as $i => $page) {
            if ($page['slug'] === $slug) {
                $index = $i;
                break;
            }
        }

        return [
            $index !== null && $index > 0 ? $pages[$index - 1] : null,
            $index !== null && $index < count($pages) - 1 ? $pages[$index + 1] : null,
        ];
    }

    private function convert(string $markdown): string
    {
        $environment = new Environment([
            // Inhalte stammen ausschließlich aus dem Repository (kein User-Input)
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
            'slug_normalizer' => ['unique' => 'document'],
            'heading_permalink' => [
                'html_class' => 'wiki-anchor',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'apply_id_to_heading' => true,
                'insert' => 'after',
                'min_heading_level' => 2,
                'max_heading_level' => 4,
                'symbol' => '#',
                'title' => 'Direktlink zu diesem Abschnitt',
                'aria_hidden' => true,
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        return (new MarkdownConverter($environment))
            ->convert($markdown)
            ->getContent();
    }

    /**
     * GitHub-Callouts in Hinweisboxen umwandeln:
     * > [!NOTE] / [!TIP] / [!IMPORTANT] / [!WARNING] / [!CAUTION]
     */
    private function transformCallouts(string $html): string
    {
        $types = [
            'NOTE' => ['note', 'Hinweis', 'bi-info-circle'],
            'TIP' => ['tip', 'Tipp', 'bi-lightbulb'],
            'IMPORTANT' => ['important', 'Wichtig', 'bi-exclamation-circle'],
            'WARNING' => ['warning', 'Warnung', 'bi-exclamation-triangle'],
            'CAUTION' => ['caution', 'Achtung', 'bi-exclamation-octagon'],
        ];

        $pattern = '/<blockquote>\s*<p>\[!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]\s*(?:<br \/>\s*)?(.*?)<\/blockquote>/s';

        return preg_replace_callback($pattern, function (array $m) use ($types) {
            [$class, $label, $icon] = $types[$m[1]];

            return '<div class="wiki-callout wiki-callout-' . $class . '">'
                . '<p class="wiki-callout-title"><i class="bi ' . $icon . '"></i>' . $label . '</p>'
                . '<p>' . ltrim($m[2])
                . '</div>';
        }, $html);
    }

    /**
     * h2/h3 mit IDs als Inhaltsverzeichnis extrahieren.
     *
     * @return array<int, array{level: int, id: string, text: string}>
     */
    private function extractToc(string $html): array
    {
        $toc = [];

        if (! preg_match_all('/<h([23])[^>]*\bid="([^"]+)"[^>]*>(.*?)<\/h\1>/s', $html, $matches, PREG_SET_ORDER)) {
            return $toc;
        }

        foreach ($matches as $m) {
            $text = preg_replace('/<a[^>]*class="[^"]*wiki-anchor[^"]*"[^>]*>.*?<\/a>/s', '', $m[3]);
            $text = trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if ($text === '') {
                continue;
            }

            $toc[] = [
                'level' => (int) $m[1],
                'id' => $m[2],
                'text' => $text,
            ];
        }

        return $toc;
    }
}
