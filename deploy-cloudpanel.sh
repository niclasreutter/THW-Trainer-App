#!/bin/bash
# CloudPanel Deployment Script
# Für dev.thw-trainer.de (DEV) und thw-trainer.de (PROD)

# === KONFIGURATION ===
# Passe diese Variablen an deine Umgebung an:
SITE_USER="thw-trainer-dev"
DOMAIN="dev.thw-trainer.de"
PHP="/usr/bin/php8.5"
SITE_PATH="/home/${SITE_USER}/htdocs/${DOMAIN}"

# === DEPLOYMENT ===
cd "$SITE_PATH" || { echo "FEHLER: Pfad $SITE_PATH nicht gefunden!"; exit 1; }

echo "=== Starting CloudPanel Deployment ==="
echo "  Domain: $DOMAIN"
echo "  Path:   $SITE_PATH"
echo "  PHP:    $PHP"
echo ""

# 1. Composer Dependencies installieren (Production)
echo "📦 Installing composer dependencies..."
$PHP /usr/bin/composer install --no-dev --optimize-autoloader 2>&1

# 2. Cache leeren
echo ""
echo "🧹 Clearing caches..."
$PHP artisan config:clear
$PHP artisan cache:clear
$PHP artisan view:clear
$PHP artisan route:clear

# 3. Optimierung
echo ""
echo "⚡ Optimizing..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# 4. Migrationen ausführen
echo ""
echo "🗄️ Running migrations..."
$PHP artisan migrate --force

# 5. Storage Link (falls nötig)
echo ""
echo "🔗 Creating storage link..."
$PHP artisan storage:link 2>/dev/null || true

# 6. NPM Build (falls nötig)
if [ -f "package.json" ]; then
    echo ""
    echo "🎨 Building frontend assets..."
    npm install --production=false 2>&1
    npm run build 2>&1
fi

# 7. Queue Worker neu starten (falls Supervisor läuft)
echo ""
echo "🔄 Restarting queue worker..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart thw-trainer-worker 2>/dev/null && echo "  Queue worker restarted." || echo "  Supervisor nicht verfügbar oder Worker nicht konfiguriert – übersprungen."
else
    echo "  Supervisor nicht installiert – übersprungen."
fi

echo ""
echo "=== ✅ CloudPanel Deployment Complete ==="
