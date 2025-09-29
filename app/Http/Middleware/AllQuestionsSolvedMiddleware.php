<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
class AllQuestionsSolvedMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $total = Question::count();
        $solved = count($user->solved_questions ?? []);
        if ($solved < $total) {
            return redirect()->route('practice.show')->with('error', 'Bitte löse zuerst alle Übungsfragen!');
        }
        return $next($request);
    }
}
