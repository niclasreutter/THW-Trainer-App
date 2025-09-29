# Deployment Checklist for THW Trainer App

## Before Uploading

1. **Clear local caches:**
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   php artisan cache:clear
   php artisan clear-compiled
   ```

2. **Optimize for production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

## Files to Upload

1. **Upload all files EXCEPT:**
   - `.env` (use `.env.production` as template)
   - `node_modules/`
   - `storage/logs/*`
   - `bootstrap/cache/*`
   - `.git/`

## After Uploading

1. **Set up environment:**
   - Copy `.env.production` to `.env`
   - Fill in database and mail credentials
   - Generate app key: `php artisan key:generate`

2. **Run the cache clearing script:**
   ```bash
   php clear-caches.php
   ```

3. **Set proper permissions:**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

5. **Clear caches again:**
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   php artisan cache:clear
   ```

6. **Optimize for production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Troubleshooting

If you still get the `open_basedir` error:

1. Check that your `.env` file has the correct `APP_ENV=production`
2. Ensure all cache files are cleared
3. Contact your hosting provider about `open_basedir` restrictions
4. Consider using a different hosting solution if restrictions are too strict

## Important Notes

- Never upload your local `.env` file to production
- Always use HTTPS in production (`APP_URL=https://...`)
- Set `APP_DEBUG=false` in production
- Use strong database passwords
- Keep your application key secure
