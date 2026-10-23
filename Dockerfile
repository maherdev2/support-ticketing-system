# Optimized PHP 8.3 FPM production image
FROM php:8.3-fpm

# Install runtime dependencies only (no git, zip, curl)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copy application code
COPY --chown=www-data:www-data . .

# Set permissions
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache \
    && find . -maxdepth 1 -type f \( -name ".env*" -o -name ".git*" \) -delete

EXPOSE 9000

USER www-data

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD php -r "exit(0);" || exit 1

CMD ["php-fpm"]
