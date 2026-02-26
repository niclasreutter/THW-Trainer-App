<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LernplanController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_goal' => 'nullable|integer|min:1|max:100',
            'weekly_goal_days' => 'nullable|integer|min:1|max:7',
        ]);

        $user = $request->user();
        $user->daily_goal = $validated['daily_goal'];
        $user->weekly_goal_days = $validated['weekly_goal_days'];
        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Lernplan gespeichert.');
    }
}
