<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FailedJobController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $queueFilter = trim((string) $request->input('queue', ''));

        $query = DB::table('failed_jobs')->orderByDesc('failed_at');

        if ($queueFilter !== '') {
            $query->where('queue', $queueFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('exception', 'like', '%' . $search . '%')
                  ->orWhere('payload', 'like', '%' . $search . '%')
                  ->orWhere('queue', 'like', '%' . $search . '%');
            });
        }

        $failedJobs = $query->paginate(25)->withQueryString();

        $jobs = $failedJobs->getCollection()->map(function ($job) {
            $payload = json_decode($job->payload, true) ?: [];
            $displayName = $payload['displayName'] ?? ($payload['data']['commandName'] ?? 'Unbekannt');

            $exceptionFirstLine = '';
            $exceptionClass = '';
            if (!empty($job->exception)) {
                $lines = preg_split('/\r\n|\n|\r/', $job->exception, 2);
                $exceptionFirstLine = $lines[0] ?? '';
                if (preg_match('/^([A-Za-z0-9_\\\\]+Exception|[A-Za-z0-9_\\\\]+Error)\s*:/', $exceptionFirstLine, $m)) {
                    $exceptionClass = $m[1];
                }
            }

            return (object) [
                'id'              => $job->id,
                'uuid'            => $job->uuid,
                'connection'      => $job->connection,
                'queue'           => $job->queue,
                'job_name'        => $displayName,
                'attempts'        => $payload['attempts'] ?? null,
                'failed_at'       => $job->failed_at,
                'exception_first' => $exceptionFirstLine,
                'exception_class' => $exceptionClass,
                'exception_full'  => $job->exception,
                'payload'         => $job->payload,
            ];
        });
        $failedJobs->setCollection($jobs);

        $totalCount = DB::table('failed_jobs')->count();
        $pendingCount = DB::table('jobs')->count();
        $last24h = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDay())
            ->count();

        $availableQueues = DB::table('failed_jobs')
            ->select('queue')
            ->groupBy('queue')
            ->orderBy('queue')
            ->pluck('queue');

        return view('admin.failed-jobs.index', [
            'failedJobs'      => $failedJobs,
            'totalCount'      => $totalCount,
            'pendingCount'    => $pendingCount,
            'last24h'         => $last24h,
            'availableQueues' => $availableQueues,
            'search'          => $search,
            'queueFilter'     => $queueFilter,
        ]);
    }

    public function retry(string $id)
    {
        Artisan::call('queue:retry', ['id' => [$id]]);

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Job wurde zur erneuten Ausführung markiert.');
    }

    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Alle fehlgeschlagenen Jobs wurden zur erneuten Ausführung markiert.');
    }

    public function destroy(string $id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Job wurde gelöscht.');
    }

    public function flush()
    {
        Artisan::call('queue:flush');

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Alle fehlgeschlagenen Jobs wurden gelöscht.');
    }
}
