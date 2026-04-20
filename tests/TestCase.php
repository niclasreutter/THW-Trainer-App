<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Override createApplication() weil inferBasePath() über das vendor-Symlink
     * auf das Haupt-Repo zeigt und nicht auf den Worktree. Wir laden hier explizit
     * unsere eigene bootstrap/app.php.
     */
    public function createApplication()
    {
        $app = require dirname(__DIR__) . '/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
