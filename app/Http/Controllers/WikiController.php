<?php

namespace App\Http\Controllers;

use App\Services\WikiService;
use Illuminate\View\View;

/**
 * Öffentliches Wiki / Anleitung (ohne Login erreichbar).
 * Inhalte: Markdown-Dateien in resources/wiki/ (siehe config/wiki.php).
 */
class WikiController extends Controller
{
    public function __construct(private WikiService $wiki)
    {
    }

    public function index(): View
    {
        return $this->page('index');
    }

    public function show(string $slug): View
    {
        // /wiki/index → kanonisch /wiki
        if ($slug === 'index') {
            abort(404);
        }

        return $this->page($slug);
    }

    private function page(string $slug): View
    {
        $page = $this->wiki->render($slug);

        abort_if($page === null, 404);

        return view('wiki.show', [
            'page' => $page,
            'navigation' => $this->wiki->navigation(),
            'activeSlug' => $slug,
        ]);
    }
}
