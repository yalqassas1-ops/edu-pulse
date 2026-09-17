#!/usr/bin/env bash
# إيقاف التنفيذ في حال حدوث أي خطأ
set -e

echo "🚀 [1/5] Installing PHP dependencies for production..."
composer install --no-dev --optimize-autoloader

echo "🎨 [2/5] Building Assets (Vite/NPM)..."
if [ -f "package.json" ]; then
    npm install
    npm run build
fi

echo "⚡ [3/5] Caching Configuration & Routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔗 [4/5] Linking Storage Directory..."
php artisan storage:link || true

echo "🗄️ [5/5] Running Database Migrations..."
php artisan migrate --force

echo "✅ Build completed successfully!"