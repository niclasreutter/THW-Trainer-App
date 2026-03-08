<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopPurchase;
use App\Models\User;
use App\Services\ShopService;
use Illuminate\Support\Facades\DB;

class ShopAnalyticsController extends Controller
{
    public function index()
    {
        $shopItems = ShopService::SHOP_ITEMS;

        // Overview metrics
        $totalPurchases = ShopPurchase::count();
        $totalXpSpent = ShopPurchase::sum('price');
        $uniqueBuyers = ShopPurchase::distinct('user_id')->count('user_id');
        $avgXpPerPurchase = $totalPurchases > 0 ? round($totalXpSpent / $totalPurchases) : 0;

        // Purchases per item type
        $itemStats = ShopPurchase::select('item_type', DB::raw('COUNT(*) as purchase_count'), DB::raw('SUM(price) as total_spent'))
            ->groupBy('item_type')
            ->orderByDesc('purchase_count')
            ->get()
            ->map(function ($row) use ($shopItems) {
                $row->item_name = $shopItems[$row->item_type]['name'] ?? $row->item_type;
                $row->item_price = $shopItems[$row->item_type]['price'] ?? 0;
                $row->category = $shopItems[$row->item_type]['category'] ?? '-';
                return $row;
            });

        // Top buyers
        $topBuyers = ShopPurchase::select('user_id', DB::raw('COUNT(*) as purchase_count'), DB::raw('SUM(price) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->user = User::select('id', 'name', 'email', 'points', 'level')->find($row->user_id);
                return $row;
            })
            ->filter(fn ($row) => $row->user !== null);

        // 30-day purchase trend
        $purchaseTrend = $this->getLast30DaysPurchases();

        // Recent purchases (last 25)
        $recentPurchases = ShopPurchase::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(25)
            ->get()
            ->map(function ($purchase) use ($shopItems) {
                $purchase->item_name = $shopItems[$purchase->item_type]['name'] ?? $purchase->item_type;
                return $purchase;
            });

        // Revenue by category
        $categoryStats = ShopPurchase::select('item_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(price) as total'))
            ->groupBy('item_type')
            ->get()
            ->groupBy(function ($row) use ($shopItems) {
                return $shopItems[$row->item_type]['category'] ?? 'other';
            })
            ->map(function ($group) {
                return [
                    'count' => $group->sum('count'),
                    'total' => $group->sum('total'),
                ];
            });

        return view('admin.shop-analytics', compact(
            'totalPurchases',
            'totalXpSpent',
            'uniqueBuyers',
            'avgXpPerPurchase',
            'itemStats',
            'topBuyers',
            'purchaseTrend',
            'recentPurchases',
            'categoryStats',
            'shopItems'
        ));
    }

    private function getLast30DaysPurchases(): array
    {
        $data = ['labels' => [], 'values' => [], 'xp' => []];

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            $data['labels'][] = $day->format('d.m');
            $data['values'][] = ShopPurchase::whereBetween('created_at', [$day, $dayEnd])->count();
            $data['xp'][] = (int) ShopPurchase::whereBetween('created_at', [$day, $dayEnd])->sum('price');
        }

        return $data;
    }
}
