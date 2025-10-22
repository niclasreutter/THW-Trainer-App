# 📊 Wöchentliches Leaderboard - Feature Dokumentation

## 🎯 Übersicht

Das **wöchentliche Leaderboard** ist ein neues Feature, das User motiviert, jede Woche aktiv zu bleiben. Es zeigt zwei Ranglisten:

1. **📅 Wöchentliche Rangliste** - Punkte der aktuellen Woche (Montag - Sonntag)
2. **🌍 Gesamt-Rangliste** - Alle gesammelten Punkte (wie bisher)

---

## ✨ Features

### 1. **Wöchentliches Reset-System**
- Jede Woche beginnt am **Montag 00:00 Uhr**
- Wöchentliche Punkte werden automatisch zurückgesetzt
- Gesamtpunkte bleiben unberührt

### 2. **Tab-Navigation**
- Einfacher Wechsel zwischen Gesamt- und Wochenansicht
- Aktiver Tab wird hervorgehoben
- URL-Parameter `?tab=woche` oder `?tab=gesamt`

### 3. **Wochenanzeige**
- Zeigt aktuellen Zeitraum (z.B. "21.10.2025 - 27.10.2025")
- Countdown bis zum nächsten Reset

### 4. **Leaderboard-Ansicht**
- 🥇🥈🥉 Medaillen für Top 3
- Farbliche Hervorhebung der eigenen Position
- Zeigt Punkte, Level & Streak für jeden User

### 5. **Eigene Platzierung**
- Wenn nicht in Top 50: Separate Box mit eigenem Rang
- Zeigt vollständige Statistiken

---

## 🛠️ Technische Implementierung

### **Datenbank-Migration**

Neue Felder in `users` Tabelle:
```sql
- weekly_points (INT) - Punkte der aktuellen Woche
- weekly_reset_at (TIMESTAMP) - Wann wurde letztmalig zurückgesetzt
```

**Migration ausführen:**
```bash
php artisan migrate
```

### **Service-Methoden**

#### `GamificationService::getWeeklyLeaderboard($limit)`
Gibt wöchentliches Leaderboard zurück.

```php
$weeklyLeaderboard = $gamificationService->getWeeklyLeaderboard(50);
```

#### `GamificationService::getCurrentWeekRange()`
Gibt Start- und Enddatum der aktuellen Woche.

```php
$weekRange = $gamificationService->getCurrentWeekRange();
// ['start' => Carbon, 'end' => Carbon, 'formatted' => '21.10.2025 - 27.10.2025']
```

#### `GamificationService::updateWeeklyPoints($user, $points)`
Wird automatisch aufgerufen wenn User Punkte erhält.
Prüft ob Reset nötig ist und fügt Punkte hinzu.

---

## 🎨 UI/UX

### **Dashboard**
- Neuer Link "📊 Leaderboard" in Quick Actions
- Gradient-Design (Gelb/Orange) für Aufmerksamkeit

### **Leaderboard-Seite**
- Tab-System für Wechsel zwischen Ansichten
- Wocheninfo-Box (nur bei Wochenliste)
- Responsive Tabelle mit Icons
- Info-Box: Wie sammelt man Punkte?

### **Highlights**
- 🥇 Gold für Platz 1 (gelber Hintergrund)
- 🥈 Silber für Platz 2 (grauer Hintergrund)
- 🥉 Bronze für Platz 3 (oranger Hintergrund)
- 💙 Blauer Rand für eigene Position

---

## 📍 Routes

```php
// Leaderboard mit Tab-Parameter
GET /leaderboard?tab=gesamt    -> Gesamt-Rangliste
GET /leaderboard?tab=woche     -> Wöchentliche Rangliste
```

---

## 🔄 Auto-Reset Logik

### **Wann wird zurückgesetzt?**
1. Bei jedem `awardPoints()` Call
2. Bei Abfrage von `getWeeklyLeaderboard()`
3. Nur wenn `weekly_reset_at` < Start der aktuellen Woche

### **Was passiert beim Reset?**
- `weekly_points` → 0
- `weekly_reset_at` → aktueller Montag 00:00

**Beispiel:**
- User hat am Sonntag 500 Punkte
- Am Montag: Automatischer Reset auf 0
- Neue Punkte werden ab Montag gezählt

---

## 🎯 Verwendung

### **Im Controller:**
```php
use App\Services\GamificationService;

public function leaderboard(Request $request)
{
    $gamificationService = new GamificationService();
    
    $tab = $request->get('tab', 'gesamt');
    
    if ($tab === 'woche') {
        $leaderboard = $gamificationService->getWeeklyLeaderboard(50);
        $weekRange = $gamificationService->getCurrentWeekRange();
    } else {
        $leaderboard = $gamificationService->getLeaderboard(50);
        $weekRange = null;
    }
    
    return view('gamification.leaderboard', compact('leaderboard', 'tab', 'weekRange'));
}
```

### **Im Blade-Template:**
```blade
@if($tab === 'woche')
    <h2>Diese Woche: {{ $weekRange['formatted'] }}</h2>
    
    @foreach($leaderboard as $user)
        {{ $user->name }} - {{ $user->weekly_points }} Punkte
    @endforeach
@else
    @foreach($leaderboard as $user)
        {{ $user->name }} - {{ $user->points }} Punkte
    @endforeach
@endif
```

---

## 💡 Motivationsfaktor

### **Warum wöchentliches Leaderboard?**

1. **Fairness:** Neue User haben eine Chance gegen alte User
2. **Engagement:** Wöchentlicher Wettbewerb motiviert
3. **Fresh Start:** Jeder kann Montags neu durchstarten
4. **Streak-Kombination:** Ergänzt perfekt das Streak-System

### **Psychologischer Effekt:**
- "Diese Woche war ich Platz 1!" 🥇
- Wöchentliche Erfolge statt nur langfristige Ziele
- Erhöht User-Retention

---

## 🚀 Erweiterungsmöglichkeiten

### **Zukünftige Features:**

1. **Wöchentliche Belohnungen**
   - Top 3 erhalten Bonus-Punkte
   - Spezielle Badges für Wochensieger

2. **Monats-Leaderboard**
   - Zusätzlich zur Woche auch Monatswertung

3. **Kategorie-Leaderboards**
   - Pro Lernabschnitt separate Ranglisten
   - "Beste in Arbeitssicherheit" etc.

4. **Benachrichtigungen**
   - Push-Notification bei Positionsänderung
   - "Du wurdest überholt!" Alert

5. **Historie**
   - Vergangene Wochen archivieren
   - "Letzte Woche warst du Platz 5"

6. **Badges für Wochensiege**
   - "3x Wochensieger" Achievement
   - Hall of Fame

---

## 📊 Beispiel-Daten

### **Montag Morgen:**
```
User A: weekly_points = 0 (Reset), points = 1500
User B: weekly_points = 0 (Reset), points = 2000
User C: weekly_points = 0 (Reset), points = 800
```

### **Mittwoch:**
```
User A: weekly_points = 120, points = 1620
User B: weekly_points = 80, points = 2080
User C: weekly_points = 200, points = 1000

Wöchentliche Rangliste:
1. User C - 200 Punkte 🥇
2. User A - 120 Punkte 🥈
3. User B - 80 Punkte 🥉
```

### **Gesamt-Rangliste:**
```
1. User B - 2080 Punkte 🥇
2. User A - 1620 Punkte 🥈
3. User C - 1000 Punkte 🥉
```

---

## ✅ Checkliste

- [x] Migration erstellt und ausgeführt
- [x] Service-Methoden implementiert
- [x] Controller aktualisiert
- [x] Blade-View erstellt
- [x] User-Model aktualisiert
- [x] Dashboard-Link hinzugefügt
- [x] Auto-Reset Logik implementiert
- [x] Responsive Design
- [x] Top 3 Highlighting
- [x] Eigene Position anzeigen

---

## 🎉 Fertig!

Das wöchentliche Leaderboard ist jetzt live und ready to use! 🚀

**Zugriff:**
- Dashboard → "📊 Leaderboard" Button
- Oder direkt: `/leaderboard?tab=woche`

Viel Erfolg mit dem neuen Feature! 💪
