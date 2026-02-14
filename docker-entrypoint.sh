#!/bin/bash
set -e

# Wait for MySQL to be ready using PHP instead of mysqladmin
echo "Waiting for MySQL to be ready..."
until php -r "
try {
    \$pdo = new PDO(
        'mysql:host=mysql;port=3306',
        '${DB_USERNAME:-al_waleed_user}',
        '${DB_PASSWORD:-al_waleed_password}',
        [PDO::ATTR_TIMEOUT => 2]
    );
    \$pdo->query('SELECT 1');
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "MySQL is ready!"

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies if vendor doesn't exist
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

# Generate application key if not exists
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creating .env file..."
    if [ -f "/var/www/html/env.docker.example" ]; then
        cp /var/www/html/env.docker.example /var/www/html/.env
    elif [ -f "/var/www/html/.env.example" ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    fi
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders (only if manager doesn't exist)
echo "Running seeders..."
php artisan db:seed --class=SuperAdminSeeder --force || true

# Create storage link for public files
echo "Creating storage link..."
php artisan storage:link || true

# Optimize for production
if [ "${APP_ENV}" = "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache
    # Note: we skip route:cache if component discovery errors occur,
    # but we'll try it and let it fail gracefully if needed.
    php artisan route:cache || echo "Route caching skipped due to errors."
    php artisan view:cache
fi

# Start Apache
echo "Starting Apache..."
exec "$@"
