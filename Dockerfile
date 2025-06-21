# Use official PHP image with extensions
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application code
COPY . /var/www

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set proper permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Expose port
EXPOSE 8000

# Start Laravel
CMD npm install && npm run build && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan db:seed --class=RecipeSeeder --force && \
    php artisan db:seed --class=IngredientSeeder --force && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=8000

