#!/bin/bash

echo "🚀 تشغيل نظام الدعم الفني..."

# Wait for MySQL
echo "⏳ انتظار قاعدة البيانات..."
sleep 10

# Run migrations
echo "📊 تشغيل migrations..."
php artisan migrate --force

# Generate key if not exists
if [ ! -f .env ]; then
    echo "🔑 إنشاء .env..."
    cp .env.example .env
    php artisan key:generate
fi

# Clear cache
echo "🧹 تنظيف الذاكرة المؤقتة..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Create storage link
echo "🔗 إنشاء storage link..."
php artisan storage:link

# Set permissions
echo "🔒 ضبط الصلاحيات..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ جاهز للتشغيل!"
