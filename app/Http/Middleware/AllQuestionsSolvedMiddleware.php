<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\UserQuestionProgress;
class AllQuestionsSolvedMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $total = Question::count();
        $mastered = UserQuestionProgress::countMastered($user->id);
        if ($mastered < $total) {
            return redirect()->route('practice.show')->with('error', 'Bitte löse zuerst alle Übungsfragen!');
        }
        return $next($request);
    }
}
