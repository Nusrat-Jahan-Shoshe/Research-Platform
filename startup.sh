#!/bin/bash
# Azure App Service startup script for Co-Auth
# Runs on every container boot before PHP-FPM takes over.

set -e

APP_DIR="/home/site/wwwroot"
cd "$APP_DIR"

echo "==> [startup] Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> [startup] Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> [startup] Ensuring storage directories exist..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ "${QUEUE_WORKER_ENABLED:-false}" = "true" ]; then
    echo "==> [startup] Starting explicitly enabled queue worker..."
    nohup php artisan queue:work \
        --tries="${QUEUE_WORKER_TRIES:-3}" \
        --timeout="${QUEUE_WORKER_TIMEOUT:-90}" \
        --sleep="${QUEUE_WORKER_SLEEP:-3}" \
        --max-jobs="${QUEUE_WORKER_MAX_JOBS:-500}" \
        >> /home/LogFiles/queue-worker.log 2>&1 &
fi

if [ "${REVERB_ENABLED:-false}" = "true" ]; then
    echo "==> [startup] Starting explicitly enabled Laravel Reverb server..."
    nohup php artisan reverb:start \
        --host="${REVERB_INTERNAL_HOST:-0.0.0.0}" \
        --port="${REVERB_INTERNAL_PORT:-8081}" \
        >> /home/LogFiles/reverb.log 2>&1 &
fi

echo "==> [startup] Web instance ready. Queue/Reverb must be enabled only on dedicated worker instances."
