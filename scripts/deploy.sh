#!/usr/bin/env bash
#
# Dermovive redeploy — pull latest main and rebuild both apps in place.
# Idempotent; safe to re-run. Run as root on the droplet:
#
#   ssh root@67.207.95.138 '/var/www/dermovive/scripts/deploy.sh'
#
# See docs/DEPLOYMENT.md for the full runbook.
set -euo pipefail

APP=/var/www/dermovive
cd "$APP"

echo "==> git pull"
git fetch origin
git reset --hard origin/main

echo "==> backend"
cd "$APP/backend"
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan storage:link || true
php artisan filament:assets
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

echo "==> frontend"
cd "$APP/frontend"
npm install --no-audit --no-fund
NODE_ENV=production npm run build
# Next standalone does not bundle static/public — copy them in.
rm -rf .next/standalone/.next/static .next/standalone/public
cp -r .next/static .next/standalone/.next/static
cp -r public       .next/standalone/public
chown -R www-data:www-data "$APP/frontend/.next"

echo "==> restart services"
systemctl restart dermovive-frontend
systemctl restart dermovive-queue
nginx -t && systemctl reload nginx

echo "==> health"
sleep 3
curl -s -o /dev/null -w "  storefront /en      -> %{http_code}\n" https://dermovive-pharma.com/en || true
curl -s -o /dev/null -w "  api /api/v1/health  -> %{http_code}\n" https://dermovive-pharma.com/api/v1/health || true
echo "==> done"
