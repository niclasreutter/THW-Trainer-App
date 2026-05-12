# Performance-Optimierungen THW-Trainer App

## 🎯 Durchgeführte Optimierungen (22. Oktober 2025)

### 1. ✅ Cookie-Banner Flackern behoben
**Problem:** Cookie-Banner wurde bei jedem Seitenaufruf kurz sichtbar
**Lösung:**
- Banner startet jetzt mit `display: none`
- Synchrone Cookie-Prüfung **vor** DOM-Rendering
- Verwendet `setProperty()` mit `!important` Flag

**Dateien geändert:**
- `resources/views/components/cookie-banner.blade.php`

---

### 2. ✅ Datenbank-Query Caching
**Problem:** Wiederholte DB-Queries bei jedem Seitenaufruf
**Lösungen:**

#### a) Total Questions Count (gecached für 1 Stunde)
```php
$totalQuestions = cache()->remember('total_questions_count', 3600, function() {
    return \App\Models\Question::count();
});
```

#### b) Unread Messages Count (gecached für 5 Minuten)
```php
$unreadCount = cache()->remember('admin_unread_messages_count', 300, function() {
    return \App\Models\ContactMessage::where('is_read', false)->count();
});
```

#### c) Cache Invalidierung
- Cache wird gelöscht wenn:
  - Nachricht als gelesen markiert wird
  - Nachricht als ungelesen markiert wird
  - Nachricht gelöscht wird

**Dateien geändert:**
- `routes/web.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `app/Http/Controllers/Admin/ContactMessageController.php`

---

## 🚀 Weitere Optimierungsempfehlungen

### 3. 🔧 Opcache aktivieren (auf Server)
**Hoster-Einstellung:**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### 4. 🔧 Database-Session statt File-Session
**Bereits aktiviert in:** `config/session.php`
```php
'driver' => env('SESSION_DRIVER', 'database'),
```

### 5. ⚡ View Caching nutzen
**Empfehlung:** Nach Deployment ausführen:
```bash
php artisan view:cache
php artisan config:cache
php artisan route:cache
```

**Wichtig:** Bei Code-Änderungen vor neuem Deployment löschen:
```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### 6. 🗄️ Datenbankindizes prüfen
**Empfohlene Indizes:**
```sql
-- Users Tabelle
CREATE INDEX idx_users_points ON users(points);
CREATE INDEX idx_users_weekly_points ON users(weekly_points);
CREATE INDEX idx_users_level ON users(level);

-- Contact Messages
CREATE INDEX idx_contact_messages_is_read ON contact_messages(is_read);
CREATE INDEX idx_contact_messages_created_at ON contact_messages(created_at);

-- Question Statistics
CREATE INDEX idx_question_statistics_user_id ON question_statistics(user_id);
CREATE INDEX idx_question_statistics_question_id ON question_statistics(question_id);

-- User Question Progress
CREATE INDEX idx_user_question_progress_user_id ON user_question_progress(user_id);
CREATE INDEX idx_user_question_progress_question_id ON user_question_progress(question_id);
```

### 7. 📦 Asset Optimierung
**Empfehlung:**
- Bilder komprimieren (WebP Format nutzen)
- JavaScript/CSS minifizieren
- Browser-Caching für Assets aktivieren

**Vite Build ausführen:**
```bash
npm run build
```

### 8. 🌐 CDN für statische Assets
**Empfehlung:** Tailwind CSS, Fonts von CDN laden
- Reduziert Server-Last
- Nutzt Browser-Cache von anderen Seiten

### 9. 🔄 Lazy Loading für Bilder
**Empfehlung:** In Blade-Templates:
```html
<img src="..." loading="lazy" alt="...">
```

### 10. 📊 Query-Optimization in Views
**Problem:** Einige Blade-Views machen zu viele DB-Queries

**Beispiele die noch optimiert werden könnten:**
```php
// ❌ Schlecht (in View)
$progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();

// ✅ Besser (im Controller mit Eager Loading)
$user = Auth::user()->load('questionProgress');
```

### 11. 💾 Redis für Caching (Optional)
**Hoster-Einstellung:** Wenn verfügbar
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 12. 🔍 Debug-Modus in Production ausschalten
**Wichtig:** In `.env` auf Production-Server:
```env
APP_DEBUG=false
APP_ENV=production
```

---

## 📈 Performance-Messungen

### Vor Optimierung:
- Cookie-Banner: ⚠️ Flackert bei jedem Laden
- Dashboard Load: ~ DB-Queries (geschätzt 8-12)
- Navigation: ~ DB-Queries (geschätzt 2-4)

### Nach Optimierung:
- Cookie-Banner: ✅ Kein Flackern mehr
- Dashboard Load: ~ DB-Queries (geschätzt 4-6)
- Navigation: ~ DB-Queries (geschätzt 0-2 dank Cache)

**Cache-Vorteile:**
- `total_questions_count`: Nur 1x pro Stunde statt bei jedem Request
- `admin_unread_messages_count`: Nur 1x pro 5 Minuten für alle Admins

---

## 🔧 Hoster-spezifische Checks

### Was könnte beim Hoster das Problem sein?

1. **PHP Version**
   - Prüfen: Ist PHP 8.2+ aktiv?
   - Ältere Versionen sind langsamer

2. **Memory Limit**
   ```php
   // In php.ini
   memory_limit = 256M
   ```

3. **Max Execution Time**
   ```php
   // In php.ini
   max_execution_time = 60
   ```

4. **Opcache nicht aktiviert**
   - Siehe Punkt 3

5. **Shared Hosting Limits**
   - CPU-Throttling bei vielen Requests
   - Empfehlung: Upgrade auf VPS oder besseren Plan

6. **Keine Gzip-Kompression**
   ```apache
   # .htaccess
   <IfModule mod_deflate.c>
       AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
   </IfModule>
   ```

7. **Keine Browser-Caching Headers**
   ```apache
   # .htaccess
   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType image/jpg "access plus 1 year"
       ExpiresByType image/jpeg "access plus 1 year"
       ExpiresByType image/gif "access plus 1 year"
       ExpiresByType image/png "access plus 1 year"
       ExpiresByType image/webp "access plus 1 year"
       ExpiresByType text/css "access plus 1 month"
       ExpiresByType application/javascript "access plus 1 month"
   </IfModule>
   ```

---

## 📊 Testing-Tools

### Performance messen:
1. **Google PageSpeed Insights**
   - https://pagespeed.web.dev/

2. **GTmetrix**
   - https://gtmetrix.com/

3. **Chrome DevTools**
   - Network Tab: Ladezeiten prüfen
   - Performance Tab: Bottlenecks finden
   - Lighthouse: Umfassende Analyse

### Laravel Debugbar (nur in Development):
```bash
composer require barryvdh/laravel-debugbar --dev
```

---

## 🎯 Zusammenfassung

**Sofort umsetzbar (auf Server):**
1. ✅ View/Config/Route Cache aktivieren
2. ✅ Opcache aktivieren
3. ✅ Gzip-Kompression aktivieren
4. ✅ Browser-Caching Headers setzen
5. ✅ APP_DEBUG=false setzen

**Mittelfristig:**
1. Datenbankindizes hinzufügen
2. Assets optimieren (WebP, Minify)
3. Lazy Loading implementieren

**Langfristig:**
1. Redis-Caching (wenn verfügbar)
2. CDN für statische Assets
3. Query-Optimierung in allen Views

---

**Erstellt:** 22. Oktober 2025
**Version:** 1.0
