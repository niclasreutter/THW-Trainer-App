<?php

namespace App\Services;

use App\Models\ShopPurchase;
use App\Models\User;
use App\Models\UserAvatarAccessory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShopService
{
    const SHOP_ITEMS = [
        'streak_freeze' => [
            'name' => 'Streak Freeze',
            'description' => 'Schützt deinen Streak für einen verpassten Tag.',
            'price' => 1500,
            'icon' => 'bi-shield-fill',
            'type' => 'instant',
            'duration_days' => null,
            'max_per_week' => 2,
            'category' => 'utility',
        ],
        'glowing_name_static' => [
            'name' => 'Leuchtender Name',
            'description' => 'Dein Name leuchtet gold auf der Rangliste.',
            'price' => 3000,
            'icon' => 'bi-brightness-high-fill',
            'type' => 'timed',
            'duration_days' => 7,
            'max_per_week' => null,
            'category' => 'cosmetic',
        ],
        'glowing_name_shimmer' => [
            'name' => 'Schimmernder Name',
            'description' => 'Dein Name schimmert mit einer goldenen Animation auf der Rangliste.',
            'price' => 5000,
            'icon' => 'bi-stars',
            'type' => 'timed',
            'duration_days' => 7,
            'max_per_week' => null,
            'category' => 'cosmetic',
        ],
        'double_xp' => [
            'name' => 'Doppel-XP Boost',
            'description' => 'Erhalte 24 Stunden lang doppelte Punkte für alle Aktivitäten.',
            'price' => 4000,
            'icon' => 'bi-lightning-charge-fill',
            'type' => 'timed',
            'duration_hours' => 24,
            'max_per_week' => 1,
            'category' => 'boost',
        ],
        'profile_frame_gold' => [
            'name' => 'Goldener Profilrahmen',
            'description' => 'Ein goldener Rahmen hebt deinen Namen auf der Rangliste hervor.',
            'price' => 7500,
            'icon' => 'bi-award-fill',
            'type' => 'timed',
            'duration_days' => 14,
            'max_per_week' => null,
            'category' => 'cosmetic',
        ],
        'rank_color' => [
            'name' => 'Individuelle Rangfarbe',
            'description' => 'Wähle eine eigene Farbe für deinen Namen auf der Rangliste.',
            'price' => 7500,
            'icon' => 'bi-palette-fill',
            'type' => 'timed',
            'duration_days' => 14,
            'max_per_week' => null,
            'category' => 'cosmetic',
            'colors' => ['blue', 'purple', 'green'],
        ],
        'title' => [
            'name' => 'Exklusiver Titel',
            'description' => 'Trage einen besonderen Titel unter deinem Namen auf der Rangliste.',
            'price' => 10000,
            'icon' => 'bi-person-badge-fill',
            'type' => 'timed',
            'duration_days' => 30,
            'max_per_week' => null,
            'category' => 'cosmetic',
            'titles' => ['Meisterhelfer', 'THW-Legende', 'Wissensheld', 'Lernmaschine'],
        ],
        'weekend_boost' => [
            'name' => 'Wochenend-Krieger',
            'description' => '+50% XP am Wochenende. Stapelt nicht mit Doppel-XP.',
            'price' => 2000,
            'icon' => 'bi-calendar-event-fill',
            'type' => 'timed',
            'duration_days' => null,
            'max_per_week' => 1,
            'category' => 'boost',
        ],
        'profile_frame_diamond' => [
            'name' => 'Diamant-Profilrahmen',
            'description' => 'Ein animierter Diamant-Rahmen mit Partikeleffekt. Das ultimative Prestige-Item.',
            'price' => 20000,
            'icon' => 'bi-gem',
            'type' => 'timed',
            'duration_days' => 30,
            'max_per_week' => null,
            'category' => 'cosmetic',
        ],

        // ── Avatar-Accessoires: Brillen ──
        'acc_prescription01' => [
            'name' => 'Rundbrille',
            'description' => 'Eine klassische runde Brille für deinen Avatar.',
            'price' => 2000,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'prescription01',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_prescription02' => [
            'name' => 'Lesebrille',
            'description' => 'Eine elegante Lesebrille.',
            'price' => 2000,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'prescription02',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_round' => [
            'name' => 'Runde Brille',
            'description' => 'Retro-Brille im runden Design.',
            'price' => 2500,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'round',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_sunglasses' => [
            'name' => 'Sonnenbrille',
            'description' => 'Coole Sonnenbrille für deinen Avatar.',
            'price' => 3000,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'sunglasses',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_wayfarers' => [
            'name' => 'Wayfarer',
            'description' => 'Stilvolle Wayfarer-Brille.',
            'price' => 3000,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'wayfarers',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_kurt' => [
            'name' => 'Nerd-Brille',
            'description' => 'Eckige Nerd-Brille im Retro-Style.',
            'price' => 2500,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'kurt',
            'colors' => ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444'],
        ],
        'acc_eyepatch' => [
            'name' => 'Augenklappe',
            'description' => 'Eine verwegene Augenklappe.',
            'price' => 3500,
            'icon' => 'bi-eyeglasses',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'accessories',
            'accessory_value' => 'eyepatch',
        ],

        // ── Avatar-Accessoires: Hüte/Mützen ──
        'hat_winterHat1' => [
            'name' => 'Wintermütze',
            'description' => 'Eine warme Wintermütze mit Bommel.',
            'price' => 3000,
            'icon' => 'bi-snow2',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'winterHat1',
        ],
        'hat_winterHat02' => [
            'name' => 'Pudelmütze',
            'description' => 'Kuschelige Pudelmütze.',
            'price' => 3000,
            'icon' => 'bi-snow2',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'winterHat02',
        ],
        'hat_winterHat03' => [
            'name' => 'Strickmütze',
            'description' => 'Handgestrickte Mütze.',
            'price' => 3500,
            'icon' => 'bi-snow2',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'winterHat03',
        ],
        'hat_winterHat04' => [
            'name' => 'Beanie',
            'description' => 'Lässige Beanie-Mütze.',
            'price' => 3500,
            'icon' => 'bi-snow2',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'winterHat04',
        ],
        'hat_hat' => [
            'name' => 'Hut',
            'description' => 'Ein eleganter Hut.',
            'price' => 4000,
            'icon' => 'bi-mortarboard',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'hat',
        ],
        'hat_hijab' => [
            'name' => 'Hijab',
            'description' => 'Ein Hijab für deinen Avatar.',
            'price' => 3000,
            'icon' => 'bi-person',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'hijab',
        ],
        'hat_turban' => [
            'name' => 'Turban',
            'description' => 'Ein Turban für deinen Avatar.',
            'price' => 3500,
            'icon' => 'bi-person',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'top',
            'accessory_value' => 'turban',
        ],

        // ── Avatar-Accessoires: Bärte ──
        'facial_beardLight' => [
            'name' => 'Dreitagebart',
            'description' => 'Ein leichter Dreitagebart.',
            'price' => 2000,
            'icon' => 'bi-person-fill',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'facialHair',
            'accessory_value' => 'beardLight',
            'colors' => ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000'],
        ],
        'facial_beardMedium' => [
            'name' => 'Vollbart',
            'description' => 'Ein gepflegter Vollbart.',
            'price' => 2500,
            'icon' => 'bi-person-fill',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'facialHair',
            'accessory_value' => 'beardMedium',
            'colors' => ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000'],
        ],
        'facial_beardMajestic' => [
            'name' => 'Prachtbart',
            'description' => 'Ein majestätischer Vollbart.',
            'price' => 4000,
            'icon' => 'bi-person-fill',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'facialHair',
            'accessory_value' => 'beardMajestic',
            'colors' => ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000'],
        ],
        'facial_moustacheFancy' => [
            'name' => 'Schnurrbart',
            'description' => 'Ein feiner Schnurrbart.',
            'price' => 2500,
            'icon' => 'bi-person-fill',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'facialHair',
            'accessory_value' => 'moustacheFancy',
            'colors' => ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000'],
        ],
        'facial_moustacheMagnum' => [
            'name' => 'Magnum-Bart',
            'description' => 'Ein kräftiger Magnum-Schnurrbart.',
            'price' => 3000,
            'icon' => 'bi-person-fill',
            'type' => 'permanent',
            'duration_days' => null,
            'max_per_week' => null,
            'category' => 'accessory',
            'accessory_type' => 'facialHair',
            'accessory_value' => 'moustacheMagnum',
            'colors' => ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000'],
        ],
    ];

    public function getShopItems(): array
    {
        return self::SHOP_ITEMS;
    }

    public function getAvailableItems(User $user): array
    {
        $items = [];

        foreach (self::SHOP_ITEMS as $slug => $item) {
            $status = $this->getItemStatus($user, $slug);
            $items[$slug] = array_merge($item, [
                'slug' => $slug,
                'can_purchase' => $status['can_purchase'],
                'reason' => $status['reason'],
                'is_active' => $status['is_active'],
                'expires_at' => $status['expires_at'],
            ]);
        }

        return $items;
    }

    public function purchase(User $user, string $itemSlug, array $metadata = []): array
    {
        $item = self::SHOP_ITEMS[$itemSlug] ?? null;

        if (!$item) {
            return ['success' => false, 'message' => 'Unbekanntes Item.'];
        }

        $status = $this->getItemStatus($user, $itemSlug);
        if (!$status['can_purchase']) {
            return ['success' => false, 'message' => $status['reason']];
        }

        return DB::transaction(function () use ($user, $itemSlug, $item, $metadata) {
            $user = User::lockForUpdate()->find($user->id);

            // Nochmal Punkte prüfen innerhalb der Transaktion
            if ($user->points < $item['price']) {
                return ['success' => false, 'message' => 'Nicht genug Punkte.'];
            }

            // Punkte abziehen (Level NICHT neu berechnen)
            $user->points -= $item['price'];
            $user->total_points_spent = ($user->total_points_spent ?? 0) + $item['price'];

            // Effekt anwenden
            $expiresAt = $this->applyItemEffect($user, $itemSlug, $item, $metadata);

            $user->save();

            // Kauf loggen
            ShopPurchase::create([
                'user_id' => $user->id,
                'item_type' => $itemSlug,
                'price' => $item['price'],
                'expires_at' => $expiresAt,
                'metadata' => !empty($metadata) ? $metadata : null,
            ]);

            // Notification erstellen
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'shop_purchase',
                'title' => 'Kauf erfolgreich',
                'message' => $item['name'] . ' erworben!',
                'icon' => $item['icon'],
                'data' => ['item' => $itemSlug, 'price' => $item['price']],
            ]);

            return [
                'success' => true,
                'message' => $item['name'] . ' erfolgreich gekauft!',
                'new_balance' => $user->points,
                'item' => $itemSlug,
            ];
        });
    }

    public function getActiveEffects(User $user): array
    {
        $effects = [];
        $now = Carbon::now();

        if ($user->glowing_name_until && Carbon::parse($user->glowing_name_until)->gt($now)) {
            $type = $user->glowing_name_type === 'shimmer' ? 'glowing_name_shimmer' : 'glowing_name_static';
            $effects[] = [
                'slug' => $type,
                'name' => self::SHOP_ITEMS[$type]['name'],
                'icon' => self::SHOP_ITEMS[$type]['icon'],
                'expires_at' => Carbon::parse($user->glowing_name_until),
            ];
        }

        if ($user->double_xp_until && Carbon::parse($user->double_xp_until)->gt($now)) {
            $effects[] = [
                'slug' => 'double_xp',
                'name' => self::SHOP_ITEMS['double_xp']['name'],
                'icon' => self::SHOP_ITEMS['double_xp']['icon'],
                'expires_at' => Carbon::parse($user->double_xp_until),
            ];
        }

        if ($user->profile_frame_until && Carbon::parse($user->profile_frame_until)->gt($now)) {
            $frameType = $user->profile_frame_type === 'diamond' ? 'profile_frame_diamond' : 'profile_frame_gold';
            $effects[] = [
                'slug' => $frameType,
                'name' => self::SHOP_ITEMS[$frameType]['name'],
                'icon' => self::SHOP_ITEMS[$frameType]['icon'],
                'expires_at' => Carbon::parse($user->profile_frame_until),
            ];
        }

        if ($user->weekend_boost_until && Carbon::parse($user->weekend_boost_until)->gt($now)) {
            $effects[] = [
                'slug' => 'weekend_boost',
                'name' => self::SHOP_ITEMS['weekend_boost']['name'],
                'icon' => self::SHOP_ITEMS['weekend_boost']['icon'],
                'expires_at' => Carbon::parse($user->weekend_boost_until),
            ];
        }

        if ($user->rank_color_until && Carbon::parse($user->rank_color_until)->gt($now) && $user->rank_color) {
            $effects[] = [
                'slug' => 'rank_color',
                'name' => self::SHOP_ITEMS['rank_color']['name'],
                'icon' => self::SHOP_ITEMS['rank_color']['icon'],
                'expires_at' => Carbon::parse($user->rank_color_until),
                'color' => $user->rank_color,
            ];
        }

        if ($user->active_title_until && Carbon::parse($user->active_title_until)->gt($now) && $user->active_title) {
            $effects[] = [
                'slug' => 'title',
                'name' => self::SHOP_ITEMS['title']['name'],
                'icon' => self::SHOP_ITEMS['title']['icon'],
                'expires_at' => Carbon::parse($user->active_title_until),
                'title' => $user->active_title,
            ];
        }

        return $effects;
    }

    public function hasActiveEffect(User $user, string $effect): bool
    {
        $now = Carbon::now();

        return match ($effect) {
            'double_xp' => $user->double_xp_until && Carbon::parse($user->double_xp_until)->gt($now),
            'glowing_name' => $user->glowing_name_until && Carbon::parse($user->glowing_name_until)->gt($now),
            'profile_frame' => $user->profile_frame_until && Carbon::parse($user->profile_frame_until)->gt($now),
            'rank_color' => $user->rank_color_until && Carbon::parse($user->rank_color_until)->gt($now),
            'title' => $user->active_title_until && Carbon::parse($user->active_title_until)->gt($now),
            'weekend_boost' => $user->weekend_boost_until && Carbon::parse($user->weekend_boost_until)->gt($now),
            default => false,
        };
    }

    public function getPurchaseHistory(User $user, int $limit = 10)
    {
        return $user->shopPurchases()->limit($limit)->get();
    }

    private function getItemStatus(User $user, string $slug): array
    {
        $item = self::SHOP_ITEMS[$slug];
        $now = Carbon::now();

        // Punkte-Check
        if ($user->points < $item['price']) {
            return [
                'can_purchase' => false,
                'reason' => 'Nicht genug Punkte. Du brauchst ' . $item['price'] . ' Punkte.',
                'is_active' => false,
                'expires_at' => null,
            ];
        }

        // Permanent-Items: Besitz prüfen
        if (($item['type'] ?? '') === 'permanent') {
            $owned = UserAvatarAccessory::where('user_id', $user->id)
                ->where('accessory_slug', $slug)
                ->exists();

            if ($owned) {
                $active = $user->active_accessories ?? [];
                $paramName = $item['accessory_type'] ?? '';
                $isActive = isset($active[$paramName]) && $active[$paramName] === ($item['accessory_value'] ?? '');

                return [
                    'can_purchase' => false,
                    'reason' => 'Bereits im Besitz.',
                    'is_active' => $isActive,
                    'expires_at' => null,
                ];
            }

            return [
                'can_purchase' => true,
                'reason' => null,
                'is_active' => false,
                'expires_at' => null,
            ];
        }

        // Wochen-Limit prüfen
        if ($item['max_per_week'] ?? null) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $purchasesThisWeek = ShopPurchase::where('user_id', $user->id)
                ->where('item_type', $slug)
                ->where('created_at', '>=', $startOfWeek)
                ->count();

            if ($purchasesThisWeek >= $item['max_per_week']) {
                return [
                    'can_purchase' => false,
                    'reason' => 'Wöchentliches Limit erreicht (max. ' . $item['max_per_week'] . '/Woche).',
                    'is_active' => false,
                    'expires_at' => null,
                ];
            }
        }

        // Bereits aktiver Effekt prüfen (für kosmetische Items)
        $isActive = false;
        $expiresAt = null;

        switch ($slug) {
            case 'glowing_name_static':
                if ($user->glowing_name_until && Carbon::parse($user->glowing_name_until)->gt($now) && $user->glowing_name_type === 'static') {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->glowing_name_until);
                }
                break;
            case 'glowing_name_shimmer':
                if ($user->glowing_name_until && Carbon::parse($user->glowing_name_until)->gt($now) && $user->glowing_name_type === 'shimmer') {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->glowing_name_until);
                }
                break;
            case 'double_xp':
                if ($user->double_xp_until && Carbon::parse($user->double_xp_until)->gt($now)) {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->double_xp_until);
                }
                break;
            case 'profile_frame_gold':
                if ($user->profile_frame_until && Carbon::parse($user->profile_frame_until)->gt($now)) {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->profile_frame_until);
                }
                break;
            case 'rank_color':
                if ($user->rank_color_until && Carbon::parse($user->rank_color_until)->gt($now)) {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->rank_color_until);
                }
                break;
            case 'title':
                if ($user->active_title_until && Carbon::parse($user->active_title_until)->gt($now)) {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->active_title_until);
                }
                break;
            case 'weekend_boost':
                if ($user->weekend_boost_until && Carbon::parse($user->weekend_boost_until)->gt($now)) {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->weekend_boost_until);
                }
                break;
            case 'profile_frame_diamond':
                if ($user->profile_frame_until && Carbon::parse($user->profile_frame_until)->gt($now) && $user->profile_frame_type === 'diamond') {
                    $isActive = true;
                    $expiresAt = Carbon::parse($user->profile_frame_until);
                }
                break;
        }

        return [
            'can_purchase' => true,
            'reason' => null,
            'is_active' => $isActive,
            'expires_at' => $expiresAt,
        ];
    }

    private function applyItemEffect(User $user, string $slug, array $item, array $metadata): ?Carbon
    {
        $expiresAt = null;

        switch ($slug) {
            case 'streak_freeze':
                $user->streak_freezes_available = ($user->streak_freezes_available ?? 0) + 1;
                break;

            case 'glowing_name_static':
                $expiresAt = Carbon::now()->addDays(7);
                $user->glowing_name_until = $expiresAt;
                $user->glowing_name_type = 'static';
                break;

            case 'glowing_name_shimmer':
                $expiresAt = Carbon::now()->addDays(7);
                $user->glowing_name_until = $expiresAt;
                $user->glowing_name_type = 'shimmer';
                break;

            case 'double_xp':
                $expiresAt = Carbon::now()->addHours(24);
                $user->double_xp_until = $expiresAt;
                break;

            case 'profile_frame_gold':
                $expiresAt = Carbon::now()->addDays(14);
                $user->profile_frame_until = $expiresAt;
                $user->profile_frame_type = 'gold';
                break;

            case 'rank_color':
                $color = $metadata['color'] ?? 'blue';
                $allowedColors = $item['colors'] ?? ['blue', 'purple', 'green'];
                if (!in_array($color, $allowedColors)) {
                    $color = 'blue';
                }
                $expiresAt = Carbon::now()->addDays(14);
                $user->rank_color = $color;
                $user->rank_color_until = $expiresAt;
                break;

            case 'title':
                $title = $metadata['title'] ?? 'Meisterhelfer';
                $allowedTitles = $item['titles'] ?? [];
                if (!in_array($title, $allowedTitles)) {
                    $title = 'Meisterhelfer';
                }
                $expiresAt = Carbon::now()->addDays(30);
                $user->active_title = $title;
                $user->active_title_until = $expiresAt;
                break;

            case 'weekend_boost':
                // Läuft bis Ende des nächsten Sonntags
                $now = Carbon::now();
                if ($now->isSaturday() || $now->isSunday()) {
                    $expiresAt = $now->copy()->endOfWeek(Carbon::SUNDAY);
                } else {
                    $expiresAt = $now->copy()->next(Carbon::SUNDAY)->endOfDay();
                }
                $user->weekend_boost_until = $expiresAt;
                break;

            case 'profile_frame_diamond':
                $expiresAt = Carbon::now()->addDays(30);
                $user->profile_frame_until = $expiresAt;
                $user->profile_frame_type = 'diamond';
                break;

            default:
                // Permanente Accessoires
                if (($item['type'] ?? '') === 'permanent' && ($item['category'] ?? '') === 'accessory') {
                    UserAvatarAccessory::create([
                        'user_id' => $user->id,
                        'accessory_slug' => $slug,
                        'purchased_at' => Carbon::now(),
                    ]);
                }
                break;
        }

        return $expiresAt;
    }

    /**
     * Gibt alle gekauften Avatar-Accessoires eines Users zurück, gruppiert nach Typ.
     */
    public function getOwnedAccessories(User $user): array
    {
        $owned = UserAvatarAccessory::where('user_id', $user->id)
            ->pluck('accessory_slug')
            ->toArray();

        $active = $user->active_accessories ?? [];

        $result = [
            'accessories' => [],
            'top' => [],
            'facialHair' => [],
        ];

        foreach (self::SHOP_ITEMS as $slug => $item) {
            if (($item['type'] ?? '') !== 'permanent' || ($item['category'] ?? '') !== 'accessory') {
                continue;
            }
            if (!in_array($slug, $owned)) {
                continue;
            }

            $paramName = $item['accessory_type'];
            $isActive = isset($active[$paramName]) && $active[$paramName] === $item['accessory_value'];

            $result[$paramName][$slug] = array_merge($item, [
                'slug' => $slug,
                'is_active' => $isActive,
            ]);
        }

        return $result;
    }
}
