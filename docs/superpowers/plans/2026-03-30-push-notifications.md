# Push-Benachrichtigungen Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Browser/PWA Push-Benachrichtigungen für System-Events und Admin-Nachrichten implementieren.

**Architecture:** `laravel-notification-channels/webpush` Paket für VAPID-basierte Push-Subscriptions. Generische `PushNotification`-Klasse (Laravel Notification) mit WebPushChannel. Bestehende In-App-Notifications werden parallel als Push gesendet. Admin bekommt eigene Seite zum Senden von Freitext-Nachrichten an alle oder Ortsverbände.

**Tech Stack:** Laravel 12, laravel-notification-channels/webpush, minishlink/web-push (Dependency), Service Worker Push API, Blade + Alpine.js

---

## File Structure

### New Files
| File | Responsibility |
|------|---------------|
| `app/Notifications/PushNotification.php` | Generic Laravel Notification — accepts title/message, sends via WebPushChannel |
| `app/Http/Controllers/PushController.php` | Subscribe/unsubscribe/publicKey API endpoints |
| `app/Models/AdminPushMessage.php` | Eloquent model for admin push message history |
| `app/Http/Controllers/Admin/PushController.php` | Admin UI: send push + history |
| `resources/views/admin/push.blade.php` | Admin push send form + history table |
| `resources/views/components/push-opt-in-modal.blade.php` | Glass-card opt-in popup |
| `database/migrations/XXXX_create_admin_push_messages_table.php` | Admin push history table |
| `config/webpush.php` | Published from package (VAPID config) |

### Modified Files
| File | Change |
|------|--------|
| `composer.json` | Add `laravel-notification-channels/webpush` |
| `app/Models/User.php` | Add `HasPushSubscriptions` trait |
| `public/sw.js` | Add `push` + `notificationclick` event listeners |
| `resources/views/layouts/app.blade.php` | Add push subscription JS + include opt-in modal |
| `routes/web.php` | Add push routes + admin push routes |
| `config/services.php` | Add VAPID config section |
| `.env.example` | Add VAPID_PUBLIC_KEY + VAPID_PRIVATE_KEY |
| `app/Services/GamificationService.php` | Send push after creating notification |
| `app/Services/LeagueService.php` | Send push after creating notification |
| `app/Services/LernsessionService.php` | Send push after creating notification |
| `app/Models/OrtsverbandInvitation.php` | Send push after creating notification |

---

### Task 1: Install Package + VAPID Configuration

**Files:**
- Modify: `composer.json`
- Modify: `config/services.php:36-38`
- Modify: `.env.example`
- Create: `config/webpush.php` (via vendor:publish)

- [ ] **Step 1: Install the webpush package**

Run:
```bash
composer require laravel-notification-channels/webpush
```
Expected: Package installs successfully with `minishlink/web-push` as dependency.

- [ ] **Step 2: Publish the package migration**

Run:
```bash
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations"
```
Expected: Migration file created in `database/migrations/`

- [ ] **Step 3: Run the migration**

Run:
```bash
php artisan migrate
```
Expected: `push_subscriptions` table created with columns: `id`, `subscribable_type`, `subscribable_id`, `endpoint`, `public_key`, `auth_token`, `content_encoding`, `created_at`, `updated_at`.

- [ ] **Step 4: Generate VAPID keys**

Run:
```bash
php artisan webpush:vapid
```
Expected: `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` appended to `.env`

- [ ] **Step 5: Add VAPID config to `config/services.php`**

Add after the `slack` section (after line 36):

```php
'webpush' => [
    'public_key' => env('VAPID_PUBLIC_KEY'),
    'private_key' => env('VAPID_PRIVATE_KEY'),
    'subject' => env('VAPID_SUBJECT', 'mailto:kontakt@thw-trainer.de'),
],
```

- [ ] **Step 6: Add VAPID keys to `.env.example`**

Add at the end of `.env.example`:
```
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
```

- [ ] **Step 7: Commit**

```bash
git add composer.json composer.lock config/services.php .env.example database/migrations/*push_subscriptions*
git commit -m "✨: WebPush-Paket installieren"
```

---

### Task 2: User Model + HasPushSubscriptions Trait

**Files:**
- Modify: `app/Models/User.php:1-10` (imports) and around line 308 (relationships)

- [ ] **Step 1: Add the HasPushSubscriptions trait to User model**

In `app/Models/User.php`, add the import at the top with the other use statements:

```php
use NotificationChannels\WebPush\HasPushSubscriptions;
```

Add the trait to the class (alongside existing traits like `HasFactory`, `Notifiable`):

```php
use HasPushSubscriptions;
```

- [ ] **Step 2: Verify the trait works**

Run:
```bash
php artisan tinker --execute="echo get_class(\App\Models\User::first()->pushSubscriptions())"
```
Expected: Output showing the HasMany relationship class (no errors).

- [ ] **Step 3: Commit**

```bash
git add app/Models/User.php
git commit -m "✨: User PushSubscriptions Trait"
```

---

### Task 3: PushController (Subscribe/Unsubscribe/PublicKey)

**Files:**
- Create: `app/Http/Controllers/PushController.php`
- Modify: `routes/web.php:436` (after notification routes)

- [ ] **Step 1: Create PushController**

Create `app/Http/Controllers/PushController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    public function publicKey()
    {
        return response()->json([
            'publicKey' => config('services.webpush.public_key'),
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'content_encoding' => 'nullable|string',
        ]);

        $user = Auth::user();

        $user->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['content_encoding'] ?? 'aes128gcm'
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
        ]);

        Auth::user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['success' => true]);
    }
}
```

- [ ] **Step 2: Add push routes to `routes/web.php`**

Add after the notification routes block (after line 436), before the Lehrgang Routes comment:

```php
// Push Notification Routes
Route::get('/push/public-key', [\App\Http\Controllers\PushController::class, 'publicKey'])->name('push.public-key');
Route::post('/push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');
```

- [ ] **Step 3: Verify routes are registered**

Run:
```bash
php artisan route:list --name=push
```
Expected: Three routes listed (`push.public-key`, `push.subscribe`, `push.unsubscribe`).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/PushController.php routes/web.php
git commit -m "✨: Push Subscribe/Unsubscribe API"
```

---

### Task 4: PushNotification Class (Laravel Notification)

**Files:**
- Create: `app/Notifications/PushNotification.php`

- [ ] **Step 1: Create the generic PushNotification class**

Create `app/Notifications/PushNotification.php`:

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $title,
        private string $body,
        private ?string $url = null,
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $message = (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/logo-thw-trainer.png')
            ->badge('/logo-thw-trainer.png')
            ->tag('thw-trainer-' . substr(md5($this->title . now()), 0, 8));

        if ($this->url) {
            $message->data(['url' => $this->url]);
        }

        return $message;
    }
}
```

- [ ] **Step 2: Verify the class loads without errors**

Run:
```bash
php artisan tinker --execute="new \App\Notifications\PushNotification('Test', 'Body')"
```
Expected: Object created without errors.

- [ ] **Step 3: Commit**

```bash
git add app/Notifications/PushNotification.php
git commit -m "✨: PushNotification Klasse"
```

---

### Task 5: Service Worker Push Events

**Files:**
- Modify: `public/sw.js` (append after line 120)

- [ ] **Step 1: Add push event listener to `public/sw.js`**

Append at the end of `public/sw.js` (after the fetch event listener):

```javascript

// Push notification received
self.addEventListener('push', event => {
  if (!event.data) return;

  let data;
  try {
    data = event.data.json();
  } catch (e) {
    data = {
      title: 'THW Trainer',
      body: event.data.text()
    };
  }

  const title = data.title || 'THW Trainer';
  const options = {
    body: data.body || '',
    icon: data.icon || '/logo-thw-trainer.png',
    badge: data.badge || '/logo-thw-trainer.png',
    tag: data.tag || 'thw-trainer-default',
    data: data.data || {},
    vibrate: [200, 100, 200]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification clicked
self.addEventListener('notificationclick', event => {
  event.notification.close();

  const url = event.notification.data?.url || '/dashboard';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then(windowClients => {
        // Focus existing window if available
        for (const client of windowClients) {
          if (client.url.includes(self.location.origin) && 'focus' in client) {
            client.navigate(url);
            return client.focus();
          }
        }
        // Open new window
        return clients.openWindow(url);
      })
  );
});
```

- [ ] **Step 2: Bump the cache version to force SW update**

In `public/sw.js` line 1, change:
```javascript
const CACHE_VERSION = 'v2.0';
```
to:
```javascript
const CACHE_VERSION = 'v3.0';
```

- [ ] **Step 3: Commit**

```bash
git add public/sw.js
git commit -m "✨: Service Worker Push Events"
```

---

### Task 6: Push Opt-In Modal + Subscription JS

**Files:**
- Create: `resources/views/components/push-opt-in-modal.blade.php`
- Modify: `resources/views/layouts/app.blade.php:661-670` (SW registration section)

- [ ] **Step 1: Create the opt-in modal component**

Create `resources/views/components/push-opt-in-modal.blade.php`:

```blade
@auth
<div x-data="pushOptIn()" x-show="showModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="glass-gold p-6 rounded-2xl max-w-md w-full" @click.outside="dismiss()">
        <h3 class="text-lg font-bold text-white mb-2">Benachrichtigungen aktivieren</h3>
        <p class="text-sm text-white/70 mb-6">
            Erhalte Erinnerungen an deine Lern-Streaks, Liga-Updates und wichtige Neuigkeiten direkt auf dein Geraet.
        </p>
        <div class="flex gap-3 justify-end">
            <button @click="dismiss()" class="btn-ghost text-sm">Spaeter</button>
            <button @click="subscribe()" class="btn-primary text-sm">Aktivieren</button>
        </div>
    </div>
</div>
@endauth
```

- [ ] **Step 2: Replace the SW registration script in `app.blade.php`**

In `resources/views/layouts/app.blade.php`, replace lines 661-670 (the entire `<!-- Service Worker Registration -->` block) with:

```blade
        <!-- Push Opt-In Modal -->
        <x-push-opt-in-modal />

        <!-- Service Worker + Push Registration -->
        <script>
            function pushOptIn() {
                return {
                    showModal: false,
                    registration: null,

                    async init() {
                        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

                        try {
                            this.registration = await navigator.serviceWorker.register('/sw.js');
                            console.log('SW registered');
                        } catch (e) {
                            console.log('SW failed:', e);
                            return;
                        }

                        // Check if already subscribed
                        const subscription = await this.registration.pushManager.getSubscription();
                        if (subscription) return;

                        // Check dismiss cooldown (7 days)
                        const dismissed = localStorage.getItem('push_dismissed_at');
                        if (dismissed) {
                            const daysSince = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60 * 24);
                            if (daysSince < 7) return;
                        }

                        // Show modal after short delay
                        setTimeout(() => { this.showModal = true; }, 2000);
                    },

                    dismiss() {
                        this.showModal = false;
                        localStorage.setItem('push_dismissed_at', Date.now().toString());
                    },

                    async subscribe() {
                        this.showModal = false;
                        try {
                            // Get VAPID public key
                            const res = await fetch('{{ route("push.public-key") }}');
                            const { publicKey } = await res.json();

                            // Convert VAPID key to Uint8Array
                            const urlBase64ToUint8Array = (base64String) => {
                                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                                const raw = atob(base64);
                                const arr = new Uint8Array(raw.length);
                                for (let i = 0; i < raw.length; ++i) arr[i] = raw.charCodeAt(i);
                                return arr;
                            };

                            const subscription = await this.registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(publicKey)
                            });

                            const sub = subscription.toJSON();
                            await fetch('{{ route("push.subscribe") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    endpoint: sub.endpoint,
                                    keys: sub.keys,
                                    content_encoding: PushManager.supportedContentEncodings?.[0] || 'aes128gcm'
                                })
                            });

                            console.log('Push subscription saved');
                        } catch (e) {
                            console.error('Push subscription failed:', e);
                        }
                    }
                };
            }
        </script>
```

- [ ] **Step 3: Build assets and clear caches**

Run:
```bash
npm run build && php artisan view:clear && php artisan cache:clear
```
Expected: Build succeeds, caches cleared.

- [ ] **Step 4: Commit**

```bash
git add resources/views/components/push-opt-in-modal.blade.php resources/views/layouts/app.blade.php public/build/
git commit -m "✨: Push Opt-In Modal und JS"
```

---

### Task 7: AdminPushMessage Model + Migration

**Files:**
- Create: `app/Models/AdminPushMessage.php`
- Create: `database/migrations/XXXX_XX_XX_XXXXXX_create_admin_push_messages_table.php`

- [ ] **Step 1: Create the migration**

Run:
```bash
php artisan make:migration create_admin_push_messages_table
```

Then edit the created migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_push_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('target_type', ['all', 'ortsverband']);
            $table->foreignId('target_id')->nullable()->constrained('ortsverbände')->nullOnDelete();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_push_messages');
    }
};
```

- [ ] **Step 2: Run the migration**

Run:
```bash
php artisan migrate
```
Expected: `admin_push_messages` table created.

- [ ] **Step 3: Create the AdminPushMessage model**

Create `app/Models/AdminPushMessage.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPushMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_user_id',
        'title',
        'message',
        'target_type',
        'target_id',
        'recipients_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function ortsverband()
    {
        return $this->belongsTo(Ortsverband::class, 'target_id');
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add app/Models/AdminPushMessage.php database/migrations/*admin_push_messages*
git commit -m "✨: AdminPushMessage Model"
```

---

### Task 8: Admin Push Controller + View + Routes

**Files:**
- Create: `app/Http/Controllers/Admin/PushController.php`
- Create: `resources/views/admin/push.blade.php`
- Modify: `routes/web.php` (admin routes section, after line 578)

- [ ] **Step 1: Create the Admin PushController**

Create `app/Http/Controllers/Admin/PushController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPushMessage;
use App\Models\Ortsverband;
use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PushController extends Controller
{
    public function index()
    {
        $messages = AdminPushMessage::with('admin', 'ortsverband')
            ->orderByDesc('created_at')
            ->paginate(20);

        $ortsverbande = Ortsverband::orderBy('name')->get();

        return view('admin.push', compact('messages', 'ortsverbande'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'target_type' => 'required|in:all,ortsverband',
            'target_id' => 'required_if:target_type,ortsverband|nullable|exists:ortsverbände,id',
        ]);

        if ($validated['target_type'] === 'ortsverband') {
            $ortsverband = Ortsverband::findOrFail($validated['target_id']);
            $users = $ortsverband->members()->whereHas('pushSubscriptions')->get();
        } else {
            $users = User::whereHas('pushSubscriptions')->get();
        }

        $notification = new PushNotification(
            $validated['title'],
            $validated['message'],
        );

        Notification::send($users, $notification);

        // Also create in-app notifications
        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'admin_push',
                'title' => $validated['title'],
                'message' => $validated['message'],
            ]);
        }

        AdminPushMessage::create([
            'admin_user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_type' => $validated['target_type'],
            'target_id' => $validated['target_id'] ?? null,
            'recipients_count' => $users->count(),
        ]);

        return back()->with('success', "Push an {$users->count()} Empfaenger gesendet.");
    }
}
```

- [ ] **Step 2: Create the admin push view**

Create `resources/views/admin/push.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Push <span>Benachrichtigungen</span></h1>
        <p class="page-subtitle">Sende Push-Nachrichten an alle oder einzelne Ortsverbande</p>
    </header>

    @if(session('success'))
        <div class="glass-success p-4 rounded-xl mb-6">
            <p class="text-white text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bento-grid">
        {{-- Send Form --}}
        <div class="glass-gold bento-main">
            <h2 class="text-lg font-bold text-white mb-4">Nachricht senden</h2>

            <form method="POST" action="{{ route('admin.push.send') }}" x-data="{ targetType: 'all' }">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm text-white/70 mb-1">Titel</label>
                    <input type="text" name="title" required maxlength="100"
                           class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/40 focus:outline-none focus:border-amber-400/60"
                           placeholder="Benachrichtigungstitel" value="{{ old('title') }}">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-white/70 mb-1">Nachricht</label>
                    <textarea name="message" required maxlength="500" rows="3"
                              class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/40 focus:outline-none focus:border-amber-400/60 resize-none"
                              placeholder="Nachrichtentext">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-white/70 mb-1">Empfaenger</label>
                    <select name="target_type" x-model="targetType"
                            class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400/60">
                        <option value="all">Alle Nutzer</option>
                        <option value="ortsverband">Ortsverband</option>
                    </select>
                </div>

                <div class="mb-6" x-show="targetType === 'ortsverband'" x-cloak>
                    <label class="block text-sm text-white/70 mb-1">Ortsverband</label>
                    <select name="target_id"
                            class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400/60">
                        <option value="">Bitte waehlen...</option>
                        @foreach($ortsverbande as $ov)
                            <option value="{{ $ov->id }}">{{ $ov->name }}</option>
                        @endforeach
                    </select>
                    @error('target_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full"
                        onclick="return confirm('Push-Benachrichtigung wirklich senden?')">
                    Senden
                </button>
            </form>
        </div>

        {{-- History --}}
        <div class="glass bento-side">
            <h2 class="text-lg font-bold text-white mb-4">Verlauf</h2>

            @forelse($messages as $msg)
                <div class="border-b border-white/10 pb-3 mb-3 last:border-0">
                    <p class="text-white text-sm font-semibold">{{ $msg->title }}</p>
                    <p class="text-white/60 text-xs mt-1">{{ Str::limit($msg->message, 80) }}</p>
                    <div class="flex justify-between mt-2 text-xs text-white/40">
                        <span>{{ $msg->target_type === 'all' ? 'Alle' : $msg->ortsverband?->name }}</span>
                        <span>{{ $msg->recipients_count }} Empf.</span>
                    </div>
                    <p class="text-xs text-white/30 mt-1">{{ $msg->created_at->format('d.m.Y H:i') }} — {{ $msg->admin?->name }}</p>
                </div>
            @empty
                <p class="text-white/40 text-sm">Noch keine Nachrichten gesendet.</p>
            @endforelse

            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Add admin push routes to `routes/web.php`**

In the admin routes group (after the exam-feedback routes around line 578), add:

```php
// Push Notifications (Admin)
Route::get('push', [\App\Http\Controllers\Admin\PushController::class, 'index'])->name('push.index');
Route::post('push/send', [\App\Http\Controllers\Admin\PushController::class, 'send'])->name('push.send');
```

- [ ] **Step 4: Verify routes**

Run:
```bash
php artisan route:list --name=admin.push
```
Expected: Two routes listed (`admin.push.index`, `admin.push.send`).

- [ ] **Step 5: Build and clear caches**

Run:
```bash
npm run build && php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/PushController.php resources/views/admin/push.blade.php routes/web.php public/build/
git commit -m "✨: Admin Push-Benachrichtigungen UI"
```

---

### Task 9: Integrate Push into Existing Notification Points

**Files:**
- Modify: `app/Services/GamificationService.php:949-959` (createNotification method)
- Modify: `app/Services/LeagueService.php` (where notifications are created ~line 426)
- Modify: `app/Services/LernsessionService.php` (where notifications are created ~lines 188, 229)
- Modify: `app/Models/OrtsverbandInvitation.php` (where notifications are created ~line 120)

- [ ] **Step 1: Add a helper method to send push alongside in-app notification in GamificationService**

In `app/Services/GamificationService.php`, modify the `createNotification` method (lines 949-959) to also dispatch a push notification:

Replace the existing `createNotification` method:

```php
private function createNotification(User $user, array $data)
{
    $notification = \App\Models\Notification::create([
        'user_id' => $user->id,
        'type' => $data['type'],
        'title' => $data['title'],
        'message' => $data['message'],
        'icon' => $data['icon'] ?? null,
        'data' => $data['data'] ?? null,
    ]);

    if ($user->pushSubscriptions()->exists()) {
        $user->notify(new \App\Notifications\PushNotification(
            $data['title'],
            $data['message'],
        ));
    }

    return $notification;
}
```

- [ ] **Step 2: Add push to LeagueService notification creation**

In `app/Services/LeagueService.php`, find where `Notification::create()` is called (around line 426). After each `Notification::create()` call, add the push dispatch. The pattern is the same — after the `Notification::create([...])` call, add:

```php
if ($user->pushSubscriptions()->exists()) {
    $user->notify(new \App\Notifications\PushNotification(
        $title,  // use the same title variable from the notification
        $message, // use the same message variable
    ));
}
```

Read the file first to find the exact notification creation points and variable names.

- [ ] **Step 3: Add push to LernsessionService notification creation**

In `app/Services/LernsessionService.php`, find the notification creation points (around lines 188 and 229). After each `Notification::create()` call, add the same push dispatch pattern. Read the file first for exact variable names.

- [ ] **Step 4: Add push to OrtsverbandInvitation notification creation**

In `app/Models/OrtsverbandInvitation.php`, find the notification creation (around line 120). After the `Notification::create()` call, add the push dispatch. Read the file first for exact variable names.

- [ ] **Step 5: Verify no syntax errors**

Run:
```bash
php artisan route:list > /dev/null 2>&1 && echo "OK" || echo "FAIL"
```
Expected: `OK`

- [ ] **Step 6: Commit**

```bash
git add app/Services/GamificationService.php app/Services/LeagueService.php app/Services/LernsessionService.php app/Models/OrtsverbandInvitation.php
git commit -m "✨: Push bei System-Notifications senden"
```

---

### Task 10: Cleanup + Delete Orphaned Controller

**Files:**
- Delete: `app/Http/Controllers/PushSubscriptionController.php` (orphaned old file)

- [ ] **Step 1: Delete the orphaned PushSubscriptionController**

```bash
rm app/Http/Controllers/PushSubscriptionController.php
```

- [ ] **Step 2: Final build and cache clear**

Run:
```bash
npm run build && php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "🧹: Alten PushSubscriptionController entfernen"
```

---

## Post-Implementation Checklist

After all tasks are complete:

1. **VAPID keys** — Ensure `.env` on production has valid VAPID keys (generated via `php artisan webpush:vapid`)
2. **Queue worker** — Ensure queue worker is running in production (`php artisan queue:work`) since PushNotification implements `ShouldQueue`
3. **HTTPS** — Push API requires HTTPS (should already be the case for thw-trainer.de)
4. **Test on iOS Safari** — Open PWA, accept notification permission, verify push arrives
5. **Test on Chrome/Firefox** — Same flow, verify cross-browser
