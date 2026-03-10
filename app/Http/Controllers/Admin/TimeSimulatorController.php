<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GamificationService;
use App\Services\LeagueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TimeSimulatorController extends Controller
{
    public function __construct()
    {
        if (app()->environment('production')) {
            abort(403, 'Zeitsimulator ist nur in Test-Umgebungen verfügbar.');
        }
    }

    public function index()
    {
        $users = User::orderBy('name')->get()->map(function ($user) {
            $gamificationService = new GamificationService();
            $freezeStatus = $gamificationService->getStreakFreezeStatus($user);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'streak_days' => $user->streak_days,
                'last_activity_date' => $user->last_activity_date,
                'daily_questions_solved' => $user->daily_questions_solved,
                'daily_questions_date' => $user->daily_questions_date,
                'email_consent' => $user->email_consent,
                'freezes_remaining' => $freezeStatus['remaining'],
                'freezes_used' => $freezeStatus['used'],
                'league' => $user->league ?? 'bronze',
                'weekly_points' => $user->weekly_points ?? 0,
                'points' => $user->points ?? 0,
            ];
        });

        $leagues = LeagueService::getLeagues();

        // Liga-Statistiken
        $leagueStats = [];
        foreach ($leagues as $key => $league) {
            $leagueStats[$key] = User::where('league', $key)->count();
        }

        return view('admin.time-simulator', compact('users', 'leagues', 'leagueStats'));
    }

    public function simulateActivity(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $user = User::findOrFail($request->user_id);
        $date = Carbon::parse($request->date);
        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        $log = [];
        $log[] = "User: {$user->name} ({$user->email})";
        $log[] = "Simuliertes Datum: {$date->format('Y-m-d')}";
        $log[] = "Vorher: Streak = {$user->streak_days}, Last Activity = " . ($lastActivity ? $lastActivity->format('Y-m-d') : 'NIE');

        // Streak-Logik (vereinfacht aus GamificationService::updateStreak)
        if (!$lastActivity) {
            $user->streak_days = 1;
        } elseif ($lastActivity->isSameDay($date)) {
            $log[] = "Bereits aktiv an diesem Tag - keine Aenderung.";
        } elseif ($lastActivity->diffInDays($date) == 1) {
            $user->streak_days += 1;
        } elseif ($lastActivity->diffInDays($date) > 1) {
            // Tage verpasst ohne Freeze - Streak reset und neu starten
            $user->streak_days = 1;
            $log[] = "WARNUNG: Tage uebersprungen ohne Freeze - Streak neu gestartet bei 1.";
        }

        $user->last_activity_date = $date;
        $user->daily_questions_solved = GamificationService::STREAK_MIN_QUESTIONS;
        $user->daily_questions_date = $date;
        $user->save();

        $log[] = "Nachher: Streak = {$user->streak_days}, Last Activity = {$date->format('Y-m-d')}";
        $log[] = "Daily Questions = {$user->daily_questions_solved} (Minimum fuer Streak)";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function runDailyReset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $user = User::findOrFail($request->user_id);
        $fakeToday = Carbon::parse($request->date);
        $fakeYesterday = $fakeToday->copy()->subDay();
        $gamificationService = new GamificationService();

        $log = [];
        $log[] = "=== DAILY RESET (simuliert: {$fakeToday->format('Y-m-d')}) ===";
        $log[] = "User: {$user->name} ({$user->email})";
        $log[] = "Simuliertes Heute: {$fakeToday->format('Y-m-d')}, Gestern: {$fakeYesterday->format('Y-m-d')}";

        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;
        $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
        $log[] = "Streak: {$user->streak_days} Tage, Last Activity: {$lastActivityStr}";

        // 1. Daily Questions Reset
        if ($user->daily_questions_date && Carbon::parse($user->daily_questions_date)->lt($fakeToday)) {
            $oldDaily = $user->daily_questions_solved ?? 0;
            $user->daily_questions_solved = 0;
            $user->daily_questions_date = null;
            $log[] = "Daily Questions zurueckgesetzt: {$oldDaily} -> 0";
        }

        // 2. Streak Check
        if ($user->streak_days > 0) {
            if (!$lastActivity || $lastActivity->lt($fakeYesterday)) {
                $freezeStatus = $gamificationService->getStreakFreezeStatus($user);

                if ($freezeStatus['remaining'] > 0) {
                    $freezeResult = $gamificationService->applyManualFreeze($user);

                    if ($freezeResult['success']) {
                        $log[] = "Streak Freeze auto-eingesetzt!";
                        $log[] = "Streak: {$user->streak_days} Tage (geschuetzt)";
                        $log[] = "Verbleibende Freezes: {$freezeResult['remaining']}";

                        if ($user->email_consent) {
                            try {
                                Mail::to($user->email)->send(
                                    new \App\Mail\StreakFreezeActivatedMail($user, $user->streak_days, $freezeResult['remaining'])
                                );
                                $log[] = "E-Mail gesendet: Streak Freeze Benachrichtigung";
                            } catch (\Exception $e) {
                                $log[] = "E-Mail FEHLER: " . $e->getMessage();
                            }
                        }
                    } else {
                        $oldStreak = $user->streak_days;
                        $user->streak_days = 0;
                        $log[] = "Freeze fehlgeschlagen: {$freezeResult['message']}";
                        $log[] = "Streak zurueckgesetzt: {$oldStreak} -> 0";

                        if ($user->email_consent) {
                            try {
                                Mail::to($user->email)->send(new \App\Mail\StreakLostMail($user, $oldStreak));
                                $log[] = "E-Mail gesendet: Streak verloren";
                            } catch (\Exception $e) {
                                $log[] = "E-Mail FEHLER: " . $e->getMessage();
                            }
                        }
                    }
                } else {
                    $oldStreak = $user->streak_days;
                    $user->streak_days = 0;
                    $log[] = "Kein Freeze verfuegbar - Streak zurueckgesetzt: {$oldStreak} -> 0";

                    if ($user->email_consent) {
                        try {
                            Mail::to($user->email)->send(new \App\Mail\StreakLostMail($user, $oldStreak));
                            $log[] = "E-Mail gesendet: Streak verloren";
                        } catch (\Exception $e) {
                            $log[] = "E-Mail FEHLER: " . $e->getMessage();
                        }
                    }
                }
            } else {
                $log[] = "User war gestern aktiv - Streak bleibt bestehen.";
            }
        } else {
            $log[] = "Kein aktiver Streak - nichts zu tun.";
        }

        $user->save();

        $log[] = "=== ERGEBNIS: Streak = {$user->streak_days}, Last Activity = " . ($user->last_activity_date ? Carbon::parse($user->last_activity_date)->format('Y-m-d') : 'NIE') . " ===";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function runStreakReminder(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $user = User::findOrFail($request->user_id);
        $fakeToday = Carbon::parse($request->date);

        $log = [];
        $log[] = "=== STREAK REMINDER (simuliert: {$fakeToday->format('Y-m-d')} 18:00) ===";
        $log[] = "User: {$user->name} ({$user->email})";

        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        if ($user->streak_days < 1) {
            $log[] = "Streak = 0 - kein Reminder noetig.";
            return back()->with('simulator_log', $log)->with('simulator_status', 'info');
        }

        if (!$user->email_consent) {
            $log[] = "Keine E-Mail-Zustimmung - kein Versand.";
            return back()->with('simulator_log', $log)->with('simulator_status', 'info');
        }

        if ($lastActivity && $lastActivity->isSameDay($fakeToday)) {
            $log[] = "User war heute bereits aktiv - kein Reminder noetig.";
            return back()->with('simulator_log', $log)->with('simulator_status', 'info');
        }

        $log[] = "Streak: {$user->streak_days} Tage, Last Activity: " . ($lastActivity ? $lastActivity->format('Y-m-d') : 'NIE');
        $log[] = "User hat heute noch nicht gelernt - sende Reminder...";

        try {
            Mail::to($user->email)->send(
                new \App\Mail\StreakReminderMail($user, $user->streak_days)
            );
            $log[] = "E-Mail gesendet: Streak Reminder";
        } catch (\Exception $e) {
            $log[] = "E-Mail FEHLER: " . $e->getMessage();
        }

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function resetUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        $log = [];
        $log[] = "=== USER RESET ===";
        $log[] = "User: {$user->name} ({$user->email})";
        $log[] = "Vorher: Streak = {$user->streak_days}, Last Activity = " . ($user->last_activity_date ? Carbon::parse($user->last_activity_date)->format('Y-m-d') : 'NIE');

        $user->streak_days = 0;
        $user->last_activity_date = null;
        $user->daily_questions_solved = 0;
        $user->daily_questions_date = null;
        $user->save();

        $log[] = "Nachher: Streak = 0, Last Activity = NIE, Daily Questions = 0";
        $log[] = "User-Streak-Daten zurueckgesetzt (Punkte & Level bleiben erhalten).";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    // ==========================================
    // Liga-Simulator Aktionen
    // ==========================================

    public function setWeeklyPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'weekly_points' => 'required|integer|min:0|max:10000',
        ]);

        $user = User::findOrFail($request->user_id);
        $oldPoints = $user->weekly_points ?? 0;
        $leagueName = LeagueService::getLeagueName($user->league ?? 'bronze');

        $user->weekly_points = $request->weekly_points;
        $user->save();

        $log = [];
        $log[] = "=== WEEKLY POINTS GESETZT ===";
        $log[] = "User: {$user->name} ({$user->email})";
        $log[] = "Liga: {$leagueName}";
        $log[] = "Weekly Points: {$oldPoints} -> {$request->weekly_points}";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function setLeague(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'league' => 'required|in:' . implode(',', array_keys(LeagueService::LEAGUES)),
        ]);

        $user = User::findOrFail($request->user_id);
        $oldLeague = LeagueService::getLeagueName($user->league ?? 'bronze');
        $newLeague = LeagueService::getLeagueName($request->league);

        $user->league = $request->league;
        $user->save();

        $log = [];
        $log[] = "=== LIGA GESETZT ===";
        $log[] = "User: {$user->name} ({$user->email})";
        $log[] = "Liga: {$oldLeague} -> {$newLeague}";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function runLeagueProcessing(Request $request)
    {
        $leagueService = new LeagueService();

        $log = [];
        $log[] = "=== WOECHENTLICHE LIGA-VERARBEITUNG ===";

        // Zeige Vorher-Status
        $leagues = LeagueService::getLeagues();
        foreach ($leagues as $key => $league) {
            $usersInLeague = User::where('league', $key)
                ->where('weekly_points', '>', 0)
                ->orderBy('weekly_points', 'desc')
                ->get(['name', 'weekly_points']);

            if ($usersInLeague->isNotEmpty()) {
                $minPoints = LeagueService::PROMOTION_MIN_POINTS[$key] ?? '-';
                $log[] = "";
                $log[] = "{$league['name']}-Liga ({$usersInLeague->count()} aktive User, Aufstieg ab {$minPoints} Pkt):";
                foreach ($usersInLeague as $u) {
                    $log[] = "  {$u->name}: {$u->weekly_points} Pkt";
                }
            }
        }

        $log[] = "";
        $log[] = "Verarbeite Auf-/Abstiege...";
        $log[] = "Regeln: Aufstieg liga-spezifisch (Bronze 30%/4, Silber 25%/3, Gold 20%/2, Platin 15%/1), Abstieg Bottom " . LeagueService::RELEGATION_PERCENT . "% (max " . LeagueService::MAX_RELEGATION_SLOTS . ")";
        $log[] = "Mindestens " . LeagueService::MIN_USERS_FOR_MOVEMENT . " aktive User pro Liga noetig.";
        $log[] = "";

        $results = $leagueService->processWeeklyLeagues();

        $log[] = "=== ERGEBNIS ===";
        $log[] = "Aufstiege: {$results['promotions']}";
        $log[] = "Abstiege: {$results['relegations']}";
        $log[] = "Lootboxen vergeben: {$results['lootboxes_awarded']}";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }

    public function resetWeeklyPoints(Request $request)
    {
        $count = User::where('weekly_points', '>', 0)->count();
        User::where('weekly_points', '>', 0)->update(['weekly_points' => 0]);

        $log = [];
        $log[] = "=== WEEKLY POINTS RESET ===";
        $log[] = "Weekly Points von {$count} Usern auf 0 zurueckgesetzt.";
        $log[] = "Dies simuliert den woechentlichen Reset nach der Liga-Verarbeitung.";

        return back()->with('simulator_log', $log)->with('simulator_status', 'success');
    }
}
