<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Release vorbereiten: verschiebt die Einträge aus "## [Unreleased]" in
 * einen neuen Versionsabschnitt im CHANGELOG.md und aktualisiert die
 * VERSION-Datei sowie composer.json.
 *
 * Beispiel: php artisan app:release 1.1.0
 */
class ReleaseCommand extends Command
{
    protected $signature = 'app:release {version : Neue Versionsnummer, z.B. 1.1.0}';

    protected $description = 'Changelog-Release erstellen (CHANGELOG.md, VERSION, composer.json)';

    public function handle(): int
    {
        $version = trim((string) $this->argument('version'));
        $version = ltrim($version, 'vV');

        if (! preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            $this->error("Ungültige Version \"{$version}\" – erwartet wird X.Y.Z (Semantic Versioning).");

            return self::FAILURE;
        }

        $changelogPath = base_path('CHANGELOG.md');
        if (! is_file($changelogPath)) {
            $this->error('CHANGELOG.md nicht gefunden.');

            return self::FAILURE;
        }

        $changelog = file_get_contents($changelogPath);

        // Unreleased-Abschnitt extrahieren (bis zur nächsten "## ["-Überschrift oder Datei-Ende)
        if (! preg_match('/^## \[Unreleased\]\s*$(.*?)(?=^## \[|\z)/msu', $changelog, $m, PREG_OFFSET_CAPTURE)) {
            $this->error('Abschnitt "## [Unreleased]" nicht in CHANGELOG.md gefunden.');

            return self::FAILURE;
        }

        $unreleasedBody = trim($m[1][0]);
        if ($unreleasedBody === '') {
            $this->error('Unter "## [Unreleased]" stehen keine Einträge – nichts zu releasen.');

            return self::FAILURE;
        }

        // Letzte Version aus dem Changelog ermitteln (erste Versions-Überschrift)
        $previous = preg_match('/^## \[(\d+\.\d+\.\d+)\]/m', $changelog, $prevMatch) ? $prevMatch[1] : null;

        if ($previous !== null && version_compare($version, $previous, '<=')) {
            $this->error("Version {$version} muss größer sein als die letzte Version {$previous}.");

            return self::FAILURE;
        }

        $date = now()->format('Y-m-d');

        // Unreleased leeren und neuen Versionsabschnitt direkt darunter einfügen
        $replacement = "## [Unreleased]\n\n## [{$version}] - {$date}\n\n{$unreleasedBody}\n\n";
        $changelog = substr_replace(
            $changelog,
            $replacement,
            $m[0][1],
            strlen($m[0][0])
        );

        file_put_contents($changelogPath, rtrim($changelog) . "\n");

        // VERSION-Datei (Quelle für das Footer-Badge)
        file_put_contents(base_path('VERSION'), $version . "\n");

        // composer.json "version" synchron halten
        $composerPath = base_path('composer.json');
        $composer = file_get_contents($composerPath);
        $composer = preg_replace('/"version":\s*"[^"]*"/', "\"version\": \"{$version}\"", $composer, 1);
        file_put_contents($composerPath, $composer);

        $this->info("Release {$version} vorbereitet:");
        $this->line("  • CHANGELOG.md: [Unreleased] → [{$version}] - {$date}");
        $this->line('  • VERSION: ' . $version);
        $this->line('  • composer.json: version aktualisiert');
        $this->newLine();
        $this->line('Nächste Schritte:');
        $this->line("  git add CHANGELOG.md VERSION composer.json && git commit -m \"🔖: Release {$version}\"");
        $this->line("  git tag v{$version}");

        return self::SUCCESS;
    }
}
