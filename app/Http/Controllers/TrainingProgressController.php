<?php

namespace App\Http\Controllers;

use App\Models\UserTrainingProgress;
use App\Services\TrainingProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingProgressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $service = new TrainingProgressService();

        return view('training-progress', [
            'trainingOverview' => $service->overview($user),
            'trainingLernabschnitte' => TrainingProgressService::LERNABSCHNITTE,
            'trainingZusatzausbildungen' => TrainingProgressService::ZUSATZAUSBILDUNGEN,
        ]);
    }

    /**
     * Toggle a single training item (LA sub-item or Zusatzausbildung).
     */
    public function toggleItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_key' => ['required', 'string', Rule::in(TrainingProgressService::validItemKeys())],
            'completed' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $key = $data['item_key'];

        if ($data['completed']) {
            UserTrainingProgress::firstOrCreate(
                ['user_id' => $user->id, 'item_key' => $key],
                ['completed_at' => now()],
            );
        } else {
            UserTrainingProgress::where('user_id', $user->id)
                ->where('item_key', $key)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'overview' => (new TrainingProgressService())->overview($user),
        ]);
    }

    /**
     * Bulk-toggle an entire LA section (all sub-items).
     */
    public function toggleSection(Request $request): JsonResponse
    {
        $data = $request->validate([
            'section_key' => ['required', 'string', Rule::in(TrainingProgressService::validSectionKeys())],
            'completed' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $itemKeys = TrainingProgressService::itemKeysForSection($data['section_key']);

        if ($data['completed']) {
            $now = now();
            $rows = array_map(fn ($key) => [
                'user_id' => $user->id,
                'item_key' => $key,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $itemKeys);
            UserTrainingProgress::upsert($rows, ['user_id', 'item_key'], ['completed_at', 'updated_at']);
        } else {
            UserTrainingProgress::where('user_id', $user->id)
                ->whereIn('item_key', $itemKeys)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'overview' => (new TrainingProgressService())->overview($user),
        ]);
    }
}
