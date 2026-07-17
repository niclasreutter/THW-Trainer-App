{{-- Versionsnummer aus der VERSION-Datei, verlinkt aufs Wiki-Changelog --}}
@php $appVersion = \App\Support\AppVersion::current(); @endphp
@if ($appVersion)<a href="{{ route('landing.wiki.show', 'changelog') }}" {{ $attributes->merge(['class' => 'version-badge', 'title' => 'Was ist neu? Zum Changelog']) }}>v{{ $appVersion }}</a>@endif
