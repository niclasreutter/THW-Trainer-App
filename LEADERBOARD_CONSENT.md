# Leaderboard-Zustimmung (DSGVO-konform)

## Überblick

Das Leaderboard zeigt nun nur noch Nutzer an, die aktiv ihre Zustimmung zur Anzeige ihrer Daten im öffentlichen Leaderboard gegeben haben. Dies entspricht den DSGVO-Anforderungen für Transparenz und Kontrolle über persönliche Daten.

## Implementierte Änderungen

### 1. Datenbank-Migration
- **Datei:** `database/migrations/2025_10_22_100000_add_leaderboard_consent_to_users_table.php`
- **Neue Felder:**
  - `leaderboard_consent` (boolean, default: false)
  - `leaderboard_consent_at` (timestamp, nullable)

### 2. User Model
- **Datei:** `app/Models/User.php`
- **Änderungen:**
  - `leaderboard_consent` und `leaderboard_consent_at` zu `$fillable` hinzugefügt
  - `leaderboard_consent_at` als DateTime gecastet

### 3. Gamification Service
- **Datei:** `app/Services/GamificationService.php`
- **Änderungen:**
  - `getLeaderboard()`: Filtert nach `where('leaderboard_consent', true)`
  - `getWeeklyLeaderboard()`: Filtert nach `where('leaderboard_consent', true)`
  
**Wichtig:** Nur Nutzer mit aktiver Zustimmung erscheinen im Leaderboard!

### 4. Profil-Seite
- **Datei:** `resources/views/profile.blade.php`
- **Neue Sektion:** "Leaderboard-Teilnahme"
  - Checkbox für Leaderboard-Zustimmung
  - Erklärtext mit Datenschutz-Information
  - Anzeige des Zustimmungsdatums (wenn erteilt)

### 5. Profil-Controller
- **Datei:** `app/Http/Controllers/ProfileController.php`
- **Änderungen:**
  - Validierung für `leaderboard_consent` hinzugefügt
  - Automatisches Setzen von `leaderboard_consent_at` bei Zustimmung
  - Automatisches Löschen von `leaderboard_consent_at` bei Ablehnung

## Funktionsweise

### Standard-Einstellung
- Neue Nutzer: `leaderboard_consent = false` (nicht im Leaderboard sichtbar)
- Bestehende Nutzer: `leaderboard_consent = false` (müssen aktiv zustimmen)

### Opt-In Prozess
1. Nutzer besucht Profil-Seite
2. Aktiviert Checkbox "Ich möchte im öffentlichen Leaderboard erscheinen"
3. Speichert Profil
4. `leaderboard_consent = true` und `leaderboard_consent_at = NOW()`
5. Nutzer erscheint ab sofort im Leaderboard

### Opt-Out Prozess
1. Nutzer besucht Profil-Seite
2. Deaktiviert Checkbox
3. Speichert Profil
4. `leaderboard_consent = false` und `leaderboard_consent_at = NULL`
5. Nutzer verschwindet sofort aus dem Leaderboard

### Datenschutz-Vorteile
- ✅ Transparenz: Nutzer sehen klar, was mit ihren Daten passiert
- ✅ Kontrolle: Jederzeit widerrufbare Zustimmung
- ✅ Dokumentation: Zeitstempel der Zustimmung wird gespeichert
- ✅ DSGVO-konform: Opt-In statt Opt-Out

## Technische Details

### SQL-Query Beispiel (Gesamt-Leaderboard)
```php
User::where('leaderboard_consent', true)
    ->orderBy('points', 'desc')
    ->limit(100)
    ->get();
```

### SQL-Query Beispiel (Wochen-Leaderboard)
```php
User::where('leaderboard_consent', true)
    ->where('weekly_points', '>', 0)
    ->orderBy('weekly_points', 'desc')
    ->limit(100)
    ->get();
```

### Performance-Hinweis
Die `leaderboard_consent`-Spalte ist **nicht** indiziert, da:
- Das Leaderboard bereits nach `points` bzw. `weekly_points` sortiert (beide indiziert)
- Die WHERE-Bedingung sehr selektiv ist (die meisten aktiven Nutzer stimmen zu)
- Ein zusätzlicher Index die Schreibgeschwindigkeit verlangsamen würde

Falls später Performance-Probleme auftreten, kann ein Index hinzugefügt werden:
```sql
ALTER TABLE users ADD INDEX idx_leaderboard_consent (leaderboard_consent);
```

## Testing

### Manuelle Test-Szenarien

1. **Neuer Nutzer (ohne Zustimmung)**
   - Registrierung durchführen
   - Leaderboard aufrufen
   - ✅ Nutzer sollte **nicht** sichtbar sein

2. **Zustimmung erteilen**
   - Profil öffnen
   - Checkbox aktivieren
   - Speichern
   - Leaderboard aufrufen
   - ✅ Nutzer sollte sichtbar sein

3. **Zustimmung widerrufen**
   - Profil öffnen
   - Checkbox deaktivieren
   - Speichern
   - Leaderboard aufrufen
   - ✅ Nutzer sollte **nicht mehr** sichtbar sein

4. **Zeitstempel-Anzeige**
   - Profil öffnen mit aktiver Zustimmung
   - ✅ "Zustimmung erteilt am [Datum]" sollte angezeigt werden

### Automatisierte Tests (TODO)
```php
// tests/Feature/LeaderboardConsentTest.php
public function test_user_without_consent_not_in_leaderboard()
{
    $user = User::factory()->create(['leaderboard_consent' => false]);
    $response = $this->get('/gamification/leaderboard');
    $response->assertDontSeeText($user->name);
}

public function test_user_with_consent_in_leaderboard()
{
    $user = User::factory()->create(['leaderboard_consent' => true]);
    $response = $this->get('/gamification/leaderboard');
    $response->assertSeeText($user->name);
}
```

## Migration bestehender Nutzer

### Option 1: Alle Nutzer ohne Zustimmung (empfohlen)
```sql
-- Nichts tun, default ist bereits false
```

### Option 2: Aktive Nutzer mit Zustimmung
```sql
UPDATE users 
SET leaderboard_consent = 1, 
    leaderboard_consent_at = NOW() 
WHERE points > 100 
  AND last_login_at > DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Option 3: Alle bisherigen Nutzer mit Zustimmung
```sql
UPDATE users 
SET leaderboard_consent = 1, 
    leaderboard_consent_at = NOW();
```

**Empfehlung:** Option 1 verwenden (Standard: false) und Nutzer per E-Mail informieren, dass sie die Zustimmung in ihrem Profil erteilen können.

## Rechtliche Hinweise

### DSGVO-Compliance
- ✅ Art. 6 Abs. 1 lit. a DSGVO: Einwilligung als Rechtsgrundlage
- ✅ Art. 7 DSGVO: Dokumentation der Einwilligung (Zeitstempel)
- ✅ Art. 13 DSGVO: Transparenz (Erklärtext in Profil)
- ✅ Art. 17 DSGVO: Recht auf Vergessenwerden (Opt-Out jederzeit möglich)

### Datenschutzerklärung anpassen
Folgende Punkte sollten in der Datenschutzerklärung aufgenommen werden:

```
**Leaderboard-Funktion**

Wenn Sie der Teilnahme am öffentlichen Leaderboard zustimmen, werden folgende Daten 
für andere registrierte Nutzer sichtbar:
- Ihr Benutzername
- Ihre Gesamtpunktzahl
- Ihre wöchentliche Punktzahl
- Ihre Position im Ranking

Sie können diese Einwilligung jederzeit in Ihrem Profil unter 
"Leaderboard-Teilnahme" widerrufen. Nach dem Widerruf werden Ihre Daten 
sofort aus dem öffentlichen Leaderboard entfernt.

Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO (Einwilligung)
```

## Support & Troubleshooting

### Problem: Nutzer erscheint nicht im Leaderboard trotz Zustimmung
**Lösung:**
```sql
SELECT leaderboard_consent, leaderboard_consent_at, points, weekly_points 
FROM users 
WHERE email = 'nutzer@example.com';
```
- Prüfen ob `leaderboard_consent = 1`
- Prüfen ob Nutzer Punkte hat (Leaderboard zeigt nur Nutzer mit Punkten)

### Problem: Zeitstempel wird nicht gespeichert
**Lösung:**
- Prüfen ob Migration ausgeführt wurde: `php artisan migrate:status`
- Prüfen ob Feld in Model als datetime gecastet ist

### Problem: Performance-Probleme beim Leaderboard
**Lösung:**
```sql
-- Index hinzufügen
ALTER TABLE users ADD INDEX idx_leaderboard_consent (leaderboard_consent);

-- Query-Performance prüfen
EXPLAIN SELECT * FROM users 
WHERE leaderboard_consent = 1 
ORDER BY points DESC 
LIMIT 100;
```

## Changelog

### Version 1.0 (2025-10-22)
- ✅ Datenbank-Migration erstellt
- ✅ User Model angepasst
- ✅ Gamification Service aktualisiert
- ✅ Profil-Seite mit Consent-Checkbox
- ✅ Profil-Controller erweitert
- ✅ Dokumentation erstellt

## Nächste Schritte

### Kurzfristig
- [ ] Bestehende Nutzer per E-Mail über neue Funktion informieren
- [ ] Datenschutzerklärung aktualisieren
- [ ] Automatisierte Tests schreiben

### Mittelfristig
- [ ] Analytics: Wie viele Nutzer stimmen zu?
- [ ] Opt-In Banner auf Dashboard anzeigen
- [ ] Leaderboard-Preview ohne Anmeldung (Top 10)

### Langfristig
- [ ] Granulare Zustimmung (nur Wochen-Leaderboard, nur Gesamt-Leaderboard)
- [ ] Anonyme Teilnahme mit Pseudonym
- [ ] Freundesliste für privates Ranking
