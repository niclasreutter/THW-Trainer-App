# Leaderboard Dashboard Popup Modal

## Überblick

Dieses Feature zeigt neuen und bestehenden Nutzern ein **echtes Modal-Popup** auf dem Dashboard, um sie über das neue Leaderboard-Feature zu informieren und zur Teilnahme einzuladen.

## Unterschied zu Banner

- **Vorher:** Banner (Teil des Contents, scrollbar)
- **Jetzt:** Modal/Popup (schwebt über Content, blockiert Interaktion mit Hintergrund)

## Implementierung

### 1. Datenbank-Migration
- **Datei:** `database/migrations/2025_10_22_110000_add_leaderboard_banner_dismissed_to_users_table.php`
- **Neues Feld:** `leaderboard_banner_dismissed` (boolean, default: false)
- **Zweck:** Tracking ob der Nutzer das Banner bereits gesehen/beantwortet hat

### 2. User Model
- **Datei:** `app/Models/User.php`
- **Änderung:** `leaderboard_banner_dismissed` zu `$fillable` hinzugefügt

### 3. Route
- **Datei:** `routes/web.php`
- **Route:** `POST /profile/dismiss-leaderboard-banner`
- **Name:** `profile.dismiss.leaderboard.banner`

### 4. Controller
- **Datei:** `app/Http/Controllers/ProfileController.php`
- **Methode:** `dismissLeaderboardBanner(Request $request)`
- **Funktion:**
  - Setzt `leaderboard_banner_dismissed = true`
  - Bei Zustimmung: Setzt `leaderboard_consent = true` und `leaderboard_consent_at = now()`
  - Bei Ablehnung: Nur Banner ausblenden, keine Zustimmung

### 5. Dashboard View
- **Datei:** `resources/views/dashboard.blade.php`
- **Position:** Am Anfang des `@section('content')`, **über** allen anderen Inhalten
- **Bedingung:** Zeigt Modal nur wenn:
  - `!$user->leaderboard_banner_dismissed` (Modal noch nicht dismissed)
  - `!$user->leaderboard_consent` (Noch keine Zustimmung erteilt)

## Design

### Modal-Overlay
- **Full-Screen Overlay:** `position: fixed`, deckt gesamten Viewport ab
- **Hintergrund:** Dunkles Overlay mit `backdrop-filter: blur(5px)`
- **Z-Index:** 9999 (über allen anderen Elementen)
- **Centered:** Modal ist vertikal und horizontal zentriert

### Modal-Box
- **Größe:** Max-width 600px, responsive auf Mobile
- **Hintergrund:** Gradient von Amber (#fbbf24) zu Orange (#f59e0b)
- **Border-Radius:** 24px für moderne Optik
- **Shadow:** Starke Box-Shadow für Tiefe
- **Animation:** SlideUp beim Öffnen, FadeOut beim Schließen

### Farben
- **Hintergrund:** Gradient von Amber (#fbbf24) zu Orange (#f59e0b)
- **Border:** #d97706 (Amber-600)
- **Buttons:**
  - Ja-Button: Weißer Hintergrund, orangener Text, große Schrift (18px)
  - Nein-Button: Halbtransparenter weißer Hintergrund
  - Datenschutz-Link: Sehr transparenter Hintergrund
- **Close-Button:** Runder X-Button oben rechts, rotiert beim Hover

### Responsive Design
- **Desktop:** 
  - Icon oben zentral, großer Text
  - Dekorative Trophäe im Hintergrund (200px)
  - Normale Padding (32px)
  
- **Mobile:** 
  - Max-height 90vh mit Scroll bei Bedarf
  - Kleineres Padding (24px)
  - Kleinere Trophäe (120px)
  - Full-width Buttons

### Features
- ✅ Hover-Effekte auf allen Buttons
- ✅ Schließen-Button mit Rotation-Animation
- ✅ ESC-Taste zum Schließen
- ✅ Backdrop-Blur-Effekt
- ✅ SlideUp-Animation beim Öffnen
- ✅ FadeOut-Animation beim Schließen
- ✅ Click außerhalb schließt NICHT (Nutzer muss entscheiden)
- ✅ Emoji-Icons für Klarheit
- ✅ Link zur Datenschutzerklärung

## Benutzerinteraktion

### Szenario 1: Nutzer stimmt zu
1. Nutzer klickt "✅ Ja, ich möchte teilnehmen!"
2. POST-Request an `/profile/dismiss-leaderboard-banner` mit `accept=1`
3. Controller setzt:
   - `leaderboard_banner_dismissed = true`
   - `leaderboard_consent = true`
   - `leaderboard_consent_at = now()`
4. Redirect zum Dashboard
5. Banner wird **nicht mehr angezeigt**
6. Nutzer erscheint im Leaderboard

### Szenario 2: Nutzer lehnt ab
1. Nutzer klickt "❌ Nein, danke"
2. POST-Request an `/profile/dismiss-leaderboard-banner` mit `accept=0`
3. Controller setzt nur:
   - `leaderboard_banner_dismissed = true`
4. Redirect zum Dashboard
5. Banner wird **nicht mehr angezeigt**
6. Nutzer erscheint **nicht** im Leaderboard

### Szenario 3: Nutzer möchte mehr erfahren
1. Nutzer klickt "Datenschutz"
2. Öffnet Datenschutzerklärung in neuem Tab
3. Kann danach entscheiden
4. Banner bleibt sichtbar bis Entscheidung getroffen

## Änderungen im Profil

Nutzer können ihre Entscheidung jederzeit im Profil ändern:
- Leaderboard-Zustimmung aktivieren/deaktivieren
- Banner erscheint **nicht** erneut, auch wenn Zustimmung widerrufen wird

## DSGVO-Konformität

- ✅ **Opt-In statt Opt-Out:** Nutzer muss aktiv zustimmen
- ✅ **Transparenz:** Klare Erklärung was passiert
- ✅ **Widerrufbarkeit:** Jederzeit im Profil änderbar
- ✅ **Datenschutz-Link:** Direkter Zugang zu allen Informationen
- ✅ **Freiwilligkeit:** Ablehnen ohne Nachteile möglich

## Testing

### Manuelle Tests

1. **Neuer Nutzer ohne Zustimmung**
   - Registrierung ohne Leaderboard-Checkbox
   - Dashboard öffnen
   - ✅ Banner sollte sichtbar sein

2. **Banner: Zustimmung**
   - "Ja"-Button klicken
   - ✅ Redirect zum Dashboard
   - ✅ Banner nicht mehr sichtbar
   - Leaderboard öffnen
   - ✅ Nutzer sollte im Ranking erscheinen

3. **Banner: Ablehnung**
   - "Nein"-Button klicken
   - ✅ Redirect zum Dashboard
   - ✅ Banner nicht mehr sichtbar
   - Leaderboard öffnen
   - ✅ Nutzer sollte **nicht** im Ranking erscheinen

4. **Nutzer mit Zustimmung**
   - Profil öffnen
   - Leaderboard-Zustimmung aktivieren
   - Dashboard öffnen
   - ✅ Banner sollte **nicht** sichtbar sein

5. **Mobile Responsiveness**
   - Dashboard auf Handy öffnen
   - ✅ Banner sollte gut lesbar sein
   - ✅ Buttons sollten volle Breite haben
   - ✅ Kein horizontales Scrollen

### Automatisierte Tests (TODO)

```php
// tests/Feature/LeaderboardBannerTest.php

public function test_banner_shown_for_new_users()
{
    $user = User::factory()->create([
        'leaderboard_consent' => false,
        'leaderboard_banner_dismissed' => false,
    ]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertSee('Neu: Öffentliches Leaderboard!');
}

public function test_banner_not_shown_after_dismissal()
{
    $user = User::factory()->create([
        'leaderboard_banner_dismissed' => true,
    ]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertDontSee('Neu: Öffentliches Leaderboard!');
}

public function test_accept_button_sets_consent()
{
    $user = User::factory()->create([
        'leaderboard_consent' => false,
        'leaderboard_banner_dismissed' => false,
    ]);
    
    $response = $this->actingAs($user)
        ->post('/profile/dismiss-leaderboard-banner', ['accept' => 1]);
    
    $user->refresh();
    $this->assertTrue($user->leaderboard_consent);
    $this->assertTrue($user->leaderboard_banner_dismissed);
    $this->assertNotNull($user->leaderboard_consent_at);
}

public function test_decline_button_dismisses_without_consent()
{
    $user = User::factory()->create([
        'leaderboard_consent' => false,
        'leaderboard_banner_dismissed' => false,
    ]);
    
    $response = $this->actingAs($user)
        ->post('/profile/dismiss-leaderboard-banner', ['accept' => 0]);
    
    $user->refresh();
    $this->assertFalse($user->leaderboard_consent);
    $this->assertTrue($user->leaderboard_banner_dismissed);
    $this->assertNull($user->leaderboard_consent_at);
}
```

## Performance

- ✅ **Kein zusätzlicher Query:** Banner-Bedingung nutzt bereits geladenen `$user`
- ✅ **Conditional Rendering:** Banner nur geladen wenn nötig
- ✅ **Keine JavaScript-Abhängigkeit:** Funktioniert auch ohne JS
- ✅ **Single Page Load:** Banner wird beim normalen Dashboard-Aufruf geladen

## Wartung

### Banner-Text ändern
Datei: `resources/views/dashboard.blade.php`
Zeilen: ~53-113

### Banner-Styling anpassen
Datei: `resources/views/dashboard.blade.php`
Inline-Styles in der Banner-Section

### Route/Controller ändern
- Route: `routes/web.php`
- Controller: `app/Http/Controllers/ProfileController.php`
- Methode: `dismissLeaderboardBanner()`

## Changelog

### Version 1.0 (22. Oktober 2025)
- ✅ Initiale Implementierung
- ✅ Responsive Design für Desktop und Mobile
- ✅ DSGVO-konforme Opt-In-Lösung
- ✅ Datenschutz-Link integriert
- ✅ Migrations erstellt und ausgeführt

## Deployment-Hinweise

### Vor dem Deployment:
1. Migration committen
2. Controller und Route committen
3. Dashboard View committen
4. User Model committen

### Nach dem Deployment:
1. Migration auf Server ausführen: `php artisan migrate`
2. Cache leeren: `php artisan cache:clear`
3. Views neu kompilieren: `php artisan view:clear`

### Bestehende Nutzer:
- Alle bestehenden Nutzer haben `leaderboard_banner_dismissed = false`
- Sie sehen das Banner beim nächsten Dashboard-Besuch
- Einmalige Benachrichtigung über neues Feature

## Support

### Problem: Banner erscheint nicht
**Lösung:**
```sql
-- Prüfen ob Migration ausgeführt wurde
DESCRIBE users;

-- Prüfen ob Feld existiert
SELECT leaderboard_banner_dismissed, leaderboard_consent 
FROM users 
WHERE email = 'nutzer@example.com';
```

### Problem: Banner erscheint weiterhin nach Klick
**Lösung:**
```sql
-- Manuell setzen
UPDATE users 
SET leaderboard_banner_dismissed = 1 
WHERE email = 'nutzer@example.com';
```

### Problem: Nutzer möchte Banner erneut sehen
**Lösung:**
```sql
-- Banner zurücksetzen
UPDATE users 
SET leaderboard_banner_dismissed = 0 
WHERE email = 'nutzer@example.com';
```
