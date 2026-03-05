<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\ShopService;

class ShopController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shopService = new ShopService();

        $items = $shopService->getAvailableItems($user);
        $activeEffects = $shopService->getActiveEffects($user);
        $purchaseHistory = $shopService->getPurchaseHistory($user);

        return view('shop.index', compact('items', 'activeEffects', 'purchaseHistory'));
    }

    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'item' => 'required|string|max:50',
            'color' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $shopService = new ShopService();

        $metadata = [];
        if ($request->color) {
            $metadata['color'] = $request->color;
        }
        if ($request->title) {
            $metadata['title'] = $request->title;
        }

        $result = $shopService->purchase($user, $request->item, $metadata);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
