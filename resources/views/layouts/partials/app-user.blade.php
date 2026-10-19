{{-- Profil-Block der Navigation: Bild, Name, Level; daneben Mitteilungen, Farbschema, Abmelden.
     Erwartet aus layouts/app: $sidebarNotificationCount, $sidebarLevelProgress --}}
<div class="l-user">
    <a href="{{ route('profile') }}" class="l-user-card" aria-label="Profil öffnen">
        <img src="{{ auth()->user()->avatar_url }}" alt="" class="l-avatar l-avatar--lg">
        <span class="l-user-t">
            <b>{{ auth()->user()->name }}</b>
            <span class="l-small">Level {{ auth()->user()->level ?? 1 }}</span>
            <span class="l-line"><i style="width: {{ $sidebarLevelProgress }}%;"></i></span>
        </span>
    </a>
    <div class="l-user-acts">
        <a href="{{ route('notifications.index') }}" class="l-round {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" title="Mitteilungen" aria-label="Mitteilungen{{ $sidebarNotificationCount > 0 ? ', '.$sidebarNotificationCount.' ungelesen' : '' }}">
            <i class="bi bi-bell" aria-hidden="true"></i>
            @if($sidebarNotificationCount > 0)
                <i class="l-notif" aria-hidden="true"></i>
            @endif
        </a>
        <button type="button" onclick="toggleTheme()" class="l-round l-theme" title="Farbschema" aria-label="Farbschema wechseln">
            <i class="bi bi-moon-fill icon-moon" aria-hidden="true"></i>
            <i class="bi bi-sun-fill icon-sun" aria-hidden="true"></i>
            <i class="bi bi-circle-half icon-auto" aria-hidden="true"></i>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="l-round" title="Abmelden" aria-label="Abmelden">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            </button>
        </form>
    </div>
</div>
