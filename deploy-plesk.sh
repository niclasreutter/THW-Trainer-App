#!/bin/bash
# Plesk Deployment Script
# Dieses Script hochladen und in Plesk über Scheduled Tasks ausführen

cd /var/www/vhosts/thw-trainer.de/httpdocs

echo "=== Starting Deployment ==="

# 1. Composer Dependencies installieren (Production)
echo "Installing composer dependencies..."
/opt/plesk/php/8.3/bin/php /usr/bin/composer install --no-dev --optimize-autoloader

# 2. Cache leeren
echo "Clearing caches..."
/opt/plesk/php/8.3/bin/php artisan config:clear
/opt/plesk/php/8.3/bin/php artisan cache:clear
/opt/plesk/php/8.3/bin/php artisan view:clear
/opt/plesk/php/8.3/bin/php artisan route:clear

# 3. Optimierung
echo "Optimizing..."
/opt/plesk/php/8.3/bin/php artisan config:cache
/opt/plesk/php/8.3/bin/php artisan route:cache
/opt/plesk/php/8.3/bin/php artisan view:cache

# 4. Migrationen ausführen
echo "Running migrations..."
/opt/plesk/php/8.3/bin/php artisan migrate --force

# 5. Storage Link (falls nötig)
echo "Creating storage link..."
/opt/plesk/php/8.3/bin/php artisan storage:link

echo "=== Deployment Complete ==="
