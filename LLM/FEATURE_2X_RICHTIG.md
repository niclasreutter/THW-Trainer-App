# 📚 Feature: "2x Richtig in Folge" System

## 🎯 Übersicht

Seit diesem Update müssen User Fragen **2x richtig IN FOLGE** beantworten, um sie als "gemeistert" zu markieren.

### Warum diese Änderung?

- ✅ **Besseres Lernen**: Zeigt echtes Verständnis statt Glückstreffer
- ✅ **Wie bei Duolingo**: Bewährtes Lernprinzip aus erfolgreichen Apps
- ✅ **Motivation**: User sehen ihren Fortschritt klarer
- ✅ **Qualitätskontrolle**: Verhindert "durchraten"

---

## 🔧 Technische Implementierung

### Neue Datenbank-Tabelle: `user_question_progress`

```sql
CREATE TABLE user_question_progress (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    question_id BIGINT,
    consecutive_correct INT DEFAULT 0,  -- Anzahl richtiger Antworten in Folge
    last_answered_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id, question_id)
);
```

### Neues Model: `UserQuestionProgress`

```php
// Wichtigste Methoden:
$progress = UserQuestionProgress::getOrCreate($userId, $questionId);
$progress->updateProgress($isCorrect);  // +1 bei richtig, 0 bei falsch
$progress->isMastered();  // true wenn consecutive_correct >= 2
```

---

## 📊 Logik-Beispiele

### ✅ Szenario 1: Perfekter Durchlauf
```
Versuch 1: RICHTIG → consecutive_correct = 1 (noch nicht gemeistert)
Versuch 2: RICHTIG → consecutive_correct = 2 (✅ GEMEISTERT!)
```
→ Frage wird zu `solved_questions` hinzugefügt
→ Frage wird aus `exam_failed_questions` entfernt
→ User bekommt Punkte

### ❌ Szenario 2: Fehler setzt zurück
```
Versuch 1: RICHTIG → consecutive_correct = 1
Versuch 2: FALSCH  → consecutive_correct = 0 (❌ ZURÜCKGESETZT!)
Versuch 3: RICHTIG → consecutive_correct = 1
Versuch 4: RICHTIG → consecutive_correct = 2 (✅ GEMEISTERT!)
```

### 📈 Szenario 3: Nach Fehler nochmal von vorne
```
Versuch 1: FALSCH  → consecutive_correct = 0
Versuch 2: RICHTIG → consecutive_correct = 1
Versuch 3: RICHTIG → consecutive_correct = 2 (✅ GEMEISTERT!)
```

---

## 💡 User Experience

### Im Practice-Modus

**Bei 1x richtig (noch nicht gemeistert):**
```
👍 Richtig! Aber noch nicht gemeistert.
Beantworte die Frage noch 1x richtig, um sie zu meistern!
```
→ Frage bleibt in der Übungsliste
→ KEINE Punkte
→ NICHT zu solved_questions hinzugefügt

**Bei 2x richtig (gemeistert):**
```
✅ Richtig! Frage gemeistert! Weiter zur nächsten Frage...
```
→ Automatische Weiterleitung zur nächsten Frage
→ Punkte vergeben
→ Zu solved_questions hinzugefügt

**Bei falsch:**
```
❌ Leider falsch. Die richtigen Antworten sind markiert.
```
→ Fortschritt wird auf 0 zurückgesetzt
→ Frage wird ans Ende der Liste verschoben
→ Zu exam_failed_questions hinzugefügt

### In Prüfungen

- Prüfungen funktionieren **wie bisher** (keine 2x-Logik)
- ABER: Der Fortschritt wird trotzdem in `user_question_progress` getrackt
- Fragen aus Prüfungen müssen im Practice-Modus 2x richtig beantwortet werden

---

## 🔄 Datenmigration

Die Migration übernimmt automatisch bestehende Daten:

```php
// Für jede Frage in solved_questions:
→ consecutive_correct = 2 (als gemeistert markieren)

// Für jede Frage in exam_failed_questions:
→ consecutive_correct = 0 (noch nicht richtig)
```

**Wichtig:** `solved_questions` bleibt als "Cache" erhalten!

---

## 📁 Geänderte Dateien

### Backend
- ✅ `app/Models/UserQuestionProgress.php` (NEU)
- ✅ `app/Http/Controllers/PracticeController.php`
- ✅ `app/Http/Controllers/FailedPracticeController.php`
- ✅ `app/Http/Controllers/ExamController.php`
- ✅ `app/Models/User.php`
- ✅ `database/migrations/2025_10_15_000000_create_user_question_progress_table.php` (NEU)

### Frontend
- ✅ `resources/views/practice.blade.php`
- ✅ `resources/views/failed_practice.blade.php`

---

## 🚀 Deployment

### 1. Migration ausführen
```bash
php artisan migrate
```

Die Migration:
- Erstellt die neue Tabelle `user_question_progress`
- Migriert automatisch alle bestehenden `solved_questions` (mit consecutive_correct = 2)
- Migriert automatisch alle bestehenden `exam_failed_questions` (mit consecutive_correct = 0)

### 2. Testen
```bash
# Prüfe ob Tabelle existiert
php artisan tinker
> \Schema::hasTable('user_question_progress');
=> true

# Prüfe ob Daten migriert wurden
> App\Models\UserQuestionProgress::count();
```

### 3. Cache leeren (optional)
```bash
php artisan cache:clear
php artisan view:clear
```

---

## 📈 Zukünftige Erweiterungen

Mit der neuen Tabelle sind folgende Features möglich:

### 1. **Fortschrittsanzeige**
```php
// Zeige User wie viele Fragen bei 0, 1, oder 2+ richtigen Antworten sind
$progress0 = UserQuestionProgress::where('user_id', $userId)
    ->where('consecutive_correct', 0)->count();
$progress1 = UserQuestionProgress::where('user_id', $userId)
    ->where('consecutive_correct', 1)->count();
$mastered = UserQuestionProgress::where('user_id', $userId)
    ->where('consecutive_correct', '>=', 2)->count();
```

### 2. **Schwierigkeitsgrad-Analyse**
```php
// Welche Fragen sind am schwierigsten?
$hardestQuestions = UserQuestionProgress::select('question_id')
    ->groupBy('question_id')
    ->havingRaw('AVG(consecutive_correct) < 0.5')
    ->get();
```

### 3. **Lernkurve über Zeit**
```php
// Wie entwickelt sich der User?
$recentProgress = UserQuestionProgress::where('user_id', $userId)
    ->where('last_answered_at', '>=', now()->subDays(7))
    ->avg('consecutive_correct');
```

### 4. **Anpassbare Schwellenwerte**
```php
// Aktuell: 2x richtig
// Zukünftig: User-spezifisch (z.B. 3x für Admins, 1x für Anfänger)
$requiredCorrect = $user->is_admin ? 3 : 2;
$progress->isMastered($requiredCorrect);
```

---

## ⚠️ Wichtige Hinweise

### solved_questions bleibt erhalten!

**Warum?**
- ✅ Performance: `count($user->solved_questions)` ist schneller als DB-Query
- ✅ Kompatibilität: Admin-Dashboard, Middleware, etc. funktionieren weiter
- ✅ Backup: Falls etwas schief geht, haben wir noch die alten Daten
- ✅ Einfachere Migration: Weniger Code-Änderungen nötig

**Redundanz?**
Ja, aber kontrolliert:
- `solved_questions` = "Cache" für schnelle Checks
- `user_question_progress` = "Source of Truth" für Fortschritt

---

## 🐛 Troubleshooting

### Problem: Fragen werden nicht gemeistert
```php
// Prüfe Fortschritt für User + Frage
$progress = UserQuestionProgress::where('user_id', $userId)
    ->where('question_id', $questionId)
    ->first();
dd($progress->consecutive_correct);
```

### Problem: Migration schlägt fehl
```bash
# Rollback und neu versuchen
php artisan migrate:rollback --step=1
php artisan migrate
```

### Problem: Alte Daten nicht migriert
```php
// Manuelle Migration für User
$user = User::find($userId);
$solved = $user->solved_questions ?? [];

foreach ($solved as $questionId) {
    UserQuestionProgress::updateOrCreate(
        ['user_id' => $user->id, 'question_id' => $questionId],
        ['consecutive_correct' => 2, 'last_answered_at' => now()]
    );
}
```

---

## 📊 Monitoring

### Wichtige Metriken nach Deployment:

1. **Durchschnittliche Versuche bis zum Meistern**
```php
// Sollte zwischen 2-3 liegen
UserQuestionProgress::where('consecutive_correct', '>=', 2)->avg('consecutive_correct');
```

2. **User-Zufriedenheit**
- Weniger "zu einfach" Feedback?
- Mehr gemesterte Fragen über Zeit?

3. **Performance**
- DB-Queries auf `user_question_progress` schnell genug?
- Cache für häufige Abfragen nutzen?

---

## 👨‍💻 Entwickler-Notizen

### Code-Style

**✅ Gut:**
```php
$progress = UserQuestionProgress::getOrCreate($user->id, $question->id);
$progress->updateProgress($isCorrect);
if ($progress->isMastered()) {
    // ...
}
```

**❌ Vermeiden:**
```php
// NICHT direkt consecutive_correct setzen
$progress->consecutive_correct = 2;
$progress->save();

// Stattdessen:
$progress->updateProgress(true);
$progress->updateProgress(true);
```

### Tests

```php
// Feature-Test für 2x richtig Logik
public function test_question_requires_two_correct_answers()
{
    $user = User::factory()->create();
    $question = Question::factory()->create();
    
    // Erste richtige Antwort
    $this->actingAs($user)
        ->post(route('practice.submit'), [
            'question_id' => $question->id,
            'answer' => explode(',', $question->loesung)
        ]);
    
    $user->refresh();
    $this->assertNotContains($question->id, $user->solved_questions);
    
    // Zweite richtige Antwort
    $this->actingAs($user)
        ->post(route('practice.submit'), [
            'question_id' => $question->id,
            'answer' => explode(',', $question->loesung)
        ]);
    
    $user->refresh();
    $this->assertContains($question->id, $user->solved_questions);
}
```

---

## 🎉 Zusammenfassung

Das "2x richtig in Folge" Feature macht die THW-Trainer-App zu einem **echten Lern-Tool** statt nur einem Quiz!

**Key Points:**
- ✅ User müssen Fragen 2x richtig beantworten
- ✅ Bei Fehler wird Fortschritt zurückgesetzt
- ✅ Bestehende Daten werden automatisch migriert
- ✅ `solved_questions` bleibt als Performance-Cache erhalten
- ✅ Viele zukünftige Features möglich

**Nächste Schritte:**
1. Migration testen (lokal)
2. User-Feedback sammeln
3. Performance monitoren
4. Ggf. Schwellenwert anpassen (2x → 3x?)

