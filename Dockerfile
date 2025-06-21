# Base PHP image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app code
COPY . /var/www

# Remove old build artifacts if exist
RUN rm -rf node_modules public/build resources/js/.vite

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install dependencies
RUN npm install && npm run build

# Optional: check assets are created (this shows up in logs)
RUN ls -la public/build

# Set proper permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Final startup commands
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan db:seed --class=RecipeSeeder --force && \
    php artisan db:seed --class=IngredientSeeder --force && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=8000
