<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchedulerLogController extends Controller
{
    private function logPath(): string
    {
        return storage_path('logs/scheduler.log');
    }

    public function index(Request $request)
    {
        $path = $this->logPath();
        $lines = [];
        $stats = [
            'size' => 0,
            'size_formatted' => '0 KB',
            'total_lines' => 0,
            'last_modified' => null,
            'exists' => false,
        ];

        if (file_exists($path)) {
            $stats['exists'] = true;
            $stats['size'] = filesize($path);
            $stats['size_formatted'] = $stats['size'] >= 1048576
                ? number_format($stats['size'] / 1048576, 2) . ' MB'
                : number_format($stats['size'] / 1024, 1) . ' KB';
            $stats['last_modified'] = date('d.m.Y H:i', filemtime($path));

            $allLines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $stats['total_lines'] = count($allLines);

            $search = $request->input('search');
            if ($search) {
                $allLines = array_filter($allLines, function ($line) use ($search) {
                    return stripos($line, $search) !== false;
                });
            }

            $limit = min((int) ($request->input('lines', 500)), 2000);
            $lines = array_slice(array_reverse(array_values($allLines)), 0, $limit);
        }

        return view('admin.scheduler-logs.index', [
            'lines' => $lines,
            'stats' => $stats,
            'search' => $request->input('search', ''),
            'linesLimit' => $request->input('lines', 500),
        ]);
    }

    public function destroy()
    {
        $path = $this->logPath();

        if (file_exists($path)) {
            file_put_contents($path, '');
        }

        return redirect()->route('admin.scheduler-logs.index')
            ->with('success', 'Scheduler-Log wurde geleert.');
    }
}
