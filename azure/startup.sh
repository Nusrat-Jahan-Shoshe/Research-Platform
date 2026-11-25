#!/bin/bash
set -e

echo "==> Configuring Nginx site for Laravel public directory..."
cp /home/site/wwwroot/azure/nginx.conf /etc/nginx/sites-available/default
service nginx reload

cd /home/site/wwwroot

echo "==> Linking Storage..."
php artisan storage:link --force

echo "==> Caching Configurations, Routes, and Views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${QUEUE_WORKER_ENABLED:-false}" = "true" ]; then
    echo "==> Starting explicitly enabled queue worker..."
    php artisan queue:work --sleep="${QUEUE_WORKER_SLEEP:-3}" --tries="${QUEUE_WORKER_TRIES:-3}" --max-time="${QUEUE_WORKER_MAX_TIME:-3600}" > /home/site/wwwroot/storage/logs/queue.log 2>&1 &
fi

if [ "${REVERB_ENABLED:-false}" = "true" ]; then
    echo "==> Starting explicitly enabled Laravel Reverb server..."
    php artisan reverb:start --host="${REVERB_INTERNAL_HOST:-127.0.0.1}" --port="${REVERB_INTERNAL_PORT:-8081}" > /home/site/wwwroot/storage/logs/reverb.log 2>&1 &
fi

echo "==> Startup completed. Run migrations in a single deployment step; do not seed from every web instance."

