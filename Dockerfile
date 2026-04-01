FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libzip-dev libonig-dev libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml curl zip gd bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application
COPY . .

# Install dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate package discovery cache (without dev packages)
RUN php artisan package:discover --ansi || true

# Cache views
RUN php artisan view:cache 2>/dev/null || true

# Expose port
EXPOSE ${PORT:-8080}

# Start server
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
