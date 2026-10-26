{{-- Navigationsliste der App: Seitenleiste (Desktop) und Menü (Handy) nutzen dieselbe Liste.
     Erwartet aus layouts/app: $unreadLeagueNotifs, $unopenedLootboxCount, $userOV --}}
@php
    $navLink = function (string $href, bool $active, string $icon, string $label, int $badge = 0): string {
        return '<a href="'.e($href).'"'.($active ? ' aria-current="page"' : '').'>'
            .'<i class="bi bi-'.$icon.'" aria-hidden="true"></i><span>'.e($label).'</span>'
            .($badge > 0 ? '<b class="l-badge">'.$badge.'</b>' : '').'</a>';
    };
    $isAusbilderNav = $userOV && $userOV->isAusbildungsbeauftragter(auth()->user());
@endphp

<p class="l-nav-h">Navigation</p>
{!! $navLink(route('dashboard'), request()->routeIs('dashboard'), 'house-door', 'Dashboard') !!}
{!! $navLink(route('statistics'), request()->routeIs('statistics'), 'bar-chart', 'Statistiken') !!}
{!! $navLink(route('practice.menu'), request()->routeIs('practice.*'), 'book', 'Theorie lernen') !!}
{!! $navLink(route('bookmarks.index'), request()->routeIs('bookmarks.*'), 'bookmark', 'Gespeicherte Fragen') !!}
{!! $navLink(route('exam.index'), request()->routeIs('exam.index'), 'clipboard-check', 'Prüfung') !!}
{!! $navLink(route('exam.history'), request()->routeIs('exam.history*'), 'bar-chart-line', 'Prüfungshistorie') !!}
{!! $navLink(route('lehrgaenge.index'), request()->routeIs('lehrgaenge.*'), 'mortarboard', 'Lehrgänge') !!}
{!! $navLink(route('training-progress.index'), request()->routeIs('training-progress.*'), 'journal-check', 'Ausbildungsfortschritt') !!}
{!! $navLink(route('contact.index'), request()->routeIs('contact.*'), 'envelope', 'Kontakt') !!}

<p class="l-nav-h">Community</p>
{!! $navLink(route('gamification.leaderboard'), request()->routeIs('gamification.leaderboard'), 'trophy', 'Liga', $unreadLeagueNotifs) !!}
{!! $navLink(route('gamification.achievements'), request()->routeIs('gamification.achievements'), 'award', 'Achievements') !!}
{!! $navLink(route('lernsession.index'), request()->routeIs('lernsession.*'), 'people', 'Lernsessions') !!}
{!! $navLink(route('shop.index'), request()->routeIs('shop.*'), 'shop', 'Shop', $unopenedLootboxCount) !!}
{!! $navLink(route('zusatzfragen-vorschlagen.index'), request()->routeIs('zusatzfragen-vorschlagen.*'), 'lightbulb', 'Zusatz-Frage vorschlagen') !!}

<p class="l-nav-h">Ortsverband</p>
@if($isAusbilderNav)
    {!! $navLink(route('ortsverband.dashboard', $userOV), request()->routeIs('ortsverband.dashboard') || request()->routeIs('ortsverband.show'), 'building', $userOV->name) !!}
    {!! $navLink(route('ortsverband.members', $userOV), request()->routeIs('ortsverband.members') || request()->routeIs('ortsverband.members.*'), 'people', 'Mitglieder') !!}
@else
    {!! $navLink($userOV ? route('ortsverband.show', $userOV) : route('ortsverband.index'), request()->routeIs('ortsverband.show'), 'building', $userOV ? $userOV->name : 'Ortsverband') !!}
@endif

@if(auth()->user()->isAdmin() || auth()->user()->canEditQuestions())
    @php
        $openIssuesCount = cache()->remember('admin_open_issues_count', 300, fn() => \App\Models\LehrgangQuestionIssue::where('status', 'open')->count() + \App\Models\QuestionIssue::where('status', 'open')->count());
    @endphp
@endif

@if(auth()->user()->isAdmin())
    @php
        $unreadContactCount = cache()->remember('admin_unread_messages_count', 300, fn() => \App\Models\ContactMessage::where('is_read', false)->count());
    @endphp
    <p class="l-nav-h">Admin</p>
    {!! $navLink(route('admin.dashboard'), request()->routeIs('admin.dashboard'), 'speedometer2', 'Dashboard') !!}
    {!! $navLink(route('admin.statistics'), request()->routeIs('admin.statistics'), 'graph-up', 'Statistiken') !!}
    {!! $navLink(route('admin.users.index'), request()->routeIs('admin.users.*'), 'people', 'Nutzer') !!}
    {!! $navLink(route('admin.questions.index'), request()->routeIs('admin.questions.*'), 'question-circle', 'Fragen') !!}
    {!! $navLink(route('admin.lehrgaenge.index'), request()->routeIs('admin.lehrgaenge.*'), 'mortarboard', 'Lehrgänge') !!}
    {!! $navLink(route('admin.ortsverband.index'), request()->routeIs('admin.ortsverband.*'), 'building', 'Ortsverbände') !!}
    {!! $navLink(route('admin.leagues.index'), request()->routeIs('admin.leagues.*'), 'trophy', 'Ligen') !!}
    {!! $navLink(route('admin.shop-analytics'), request()->routeIs('admin.shop-analytics'), 'cart3', 'Shop Analytics') !!}
    {!! $navLink(route('admin.newsletter.index'), request()->routeIs('admin.newsletter.*'), 'megaphone', 'Newsletter') !!}
    {!! $navLink(route('admin.issues.index'), request()->routeIs('admin.issues.*'), 'exclamation-triangle', 'Fehlermeldungen', $openIssuesCount) !!}
    {!! $navLink(route('admin.contact-messages.index'), request()->routeIs('admin.contact-messages.*'), 'envelope', 'Kontaktanfragen', $unreadContactCount) !!}
    {!! $navLink(route('admin.exam-feedback.index'), request()->routeIs('admin.exam-feedback.*'), 'mortarboard', 'Prüfungs-Feedback') !!}
    {!! $navLink(route('admin.umfragen.index'), request()->routeIs('admin.umfragen.*'), 'clipboard-data', 'Umfragen') !!}
    {!! $navLink(route('admin.logs.index', 'scheduler'), request()->routeIs('admin.logs.*') && request()->route('type') === 'scheduler', 'terminal', 'Scheduler-Logs') !!}
    {!! $navLink(route('admin.logs.index', 'worker'), request()->routeIs('admin.logs.*') && request()->route('type') === 'worker', 'gear', 'Worker-Logs') !!}
    @if(!app()->environment('production'))
        {!! $navLink(route('admin.time-simulator'), request()->routeIs('admin.time-simulator*'), 'clock-history', 'Zeitsimulator') !!}
    @endif
@elseif(auth()->user()->canEditQuestions())
    @php
        $pendingSubmissionsCount = cache()->remember('admin_pending_extra_q_submissions_count', 300, fn() => \App\Models\UserExtraQuestionSubmission::where('status', 'pending')->count());
    @endphp
    <p class="l-nav-h">Fragen pflegen</p>
    {!! $navLink(route('contributor.dashboard'), request()->routeIs('contributor.dashboard'), 'speedometer2', 'Dashboard') !!}
    {!! $navLink(route('admin.questions.index'), request()->routeIs('admin.questions.*'), 'question-circle', 'Fragen') !!}
    {!! $navLink(route('admin.extra-questions.index'), request()->routeIs('admin.extra-questions.*'), 'collection', 'Zusatz-Fragen') !!}
    {!! $navLink(route('admin.extra-question-submissions.index'), request()->routeIs('admin.extra-question-submissions.*'), 'inbox', 'Zusatz-Frage-Vorschläge', $pendingSubmissionsCount) !!}
    {!! $navLink(route('admin.lehrgaenge.index'), request()->routeIs('admin.lehrgaenge.*'), 'mortarboard', 'Lehrgänge') !!}
    {!! $navLink(route('admin.issues.index'), request()->routeIs('admin.issues.*'), 'exclamation-triangle', 'Fehlermeldungen', $openIssuesCount) !!}
@endif
