{{-- Zahnrad aus dem Logo als Wasserzeichen in blauen Kopfbereichen (Styles: resources/css/public.css) --}}
@php
    $teeth = 12; $step = 2 * M_PI / $teeth; $ri = 0.84;
    $pt = fn ($r, $a) => round($r * cos($a), 4).' '.round($r * sin($a), 4);
    $gearPath = 'M'.$pt($ri, 0);
    for ($k = 0; $k < $teeth; $k++) {
        $a = $k * $step;
        $gearPath .= ' L'.$pt(1, $a + 0.08 * $step).' L'.$pt(1, $a + 0.42 * $step).' L'.$pt($ri, $a + 0.5 * $step).' A'.$ri.' '.$ri.' 0 0 1 '.$pt($ri, $a + $step);
    }
    $gearPath .= ' Z M0.6 0 A0.6 0.6 0 1 0 -0.6 0 A0.6 0.6 0 1 0 0.6 0 Z';
@endphp
<svg class="d-gear" viewBox="-1.05 -1.05 2.1 2.1" aria-hidden="true"><path d="{{ $gearPath }}" fill="currentColor" fill-rule="evenodd"/></svg>
