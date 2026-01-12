# Troubleshooting Guide

## Häufige Probleme & Lösungen

### "Route not found" (404)

```bash
php artisan route:clear
php artisan route:list | grep "name"
```
- Prüfe Route-Parameter: `route('name', [$param1, $param2])`

### "View not found"

```bash
php artisan view:clear
ls -la resources/views/path/to/view.blade.php
```
- Punkte = Verzeichnisse: `'ortsverband.lernpools.show'` = `ortsverband/lernpools/show.blade.php`

### "Column not found" (SQLSTATE[42S22])

```bash
php artisan migrate:status
php artisan migrate
```
- Prüfe Spaltenname: `lernpool_id` (NICHT `ortsverband_lernpool_id`)

### Änderungen nicht sichtbar

```bash
# Hard Refresh im Browser: Cmd+Shift+R (Mac) / Ctrl+Shift+R (Win)
npm run build
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Modal zeigt alte Daten

```javascript
// Cache-Busting hinzufügen:
const url = link.href + '?_t=' + Date.now();
fetch(url, { cache: 'no-store' });
```

### "This action is unauthorized" (403)

1. Policy prüfen: `app/Policies/[Model]Policy.php`
2. User-Rolle prüfen
3. `authorize()` Call im Controller prüfen
4. Als anderer User testen (admin, creator, member)

### JavaScript funktioniert nicht

```bash
npm run build
php artisan view:clear
```
- Browser-Console auf Fehler prüfen
- Alpine.js: `x-data` muss auf Parent-Element sein

### Tailwind-Klassen werden nicht angewendet

```bash
npm run build
php artisan view:clear
```
- Dynamische Klassen in `tailwind.config.js` safelist eintragen

### Gamification funktioniert nicht

1. `GamificationService` im Controller injected?
2. `addPoints()` wird aufgerufen?
3. `session()->flash('gamification_result', ...)` gesetzt?
4. `<x-gamification-notifications />` im View?

### E-Mail wird nicht gesendet

```bash
# .env prüfen
MAIL_MAILER=log  # für lokales Testing

# Logs checken
tail -f storage/logs/laravel.log
```

## Debug-Befehle

```bash
# Interaktive Shell
php artisan tinker
>>> $user = User::first();
>>> $user->points

# Logs verfolgen
tail -f storage/logs/laravel.log

# Alle Caches leeren
php artisan optimize:clear

# Queue-Jobs prüfen
php artisan queue:work
php artisan queue:failed
```

## Testing Checkliste

```
[ ] Happy Path funktioniert
[ ] Edge Cases (leere States, null values)
[ ] Authorization (verschiedene Rollen)
[ ] Desktop + Mobile View
[ ] Browser Console (keine Fehler)
[ ] Caches geleert
[ ] Assets gebaut (npm run build)
```
