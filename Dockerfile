# Use the official PHP image with necessary extensions
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libonig-dev libcurl4-openssl-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www

# Copy app files
COPY . .

# Clear previous node modules/build cache if any
RUN rm -rf node_modules public/build resources/js/.vite

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and compile assets with Vite
RUN npm install && npm run build

# Set permissions for Laravel directories
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Expose the app port
EXPOSE 8000

# Run Laravel app with pre-migrate & asset link
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan db:seed --class=RecipeSeeder --force && \
    php artisan db:seed --class=IngredientSeeder --force && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=8000
