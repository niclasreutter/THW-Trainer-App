<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class QuestionEditorMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->canEditQuestions()) {
            abort(403, 'Kein Zugriff');
        }
        return $next($request);
    }
}
