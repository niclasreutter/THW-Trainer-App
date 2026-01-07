<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Ortsverband;

class OrtsverbandAusbildungsbeauftragterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ortsverband = $request->route('ortsverband');
        
        // Wenn kein Ortsverband in der Route, durchlassen
        if (!$ortsverband || !($ortsverband instanceof Ortsverband)) {
            return $next($request);
        }
        
        // Prüfe ob User Ausbildungsbeauftragter ist
        if (!$ortsverband->isAusbildungsbeauftragter(auth()->user())) {
            abort(403, 'Nur Ausbildungsbeauftragte haben Zugriff auf diese Funktion.');
        }
        
        return $next($request);
    }
}
