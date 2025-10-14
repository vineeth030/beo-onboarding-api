# ===============================
# Stage 1: Build dependencies
# ===============================
FROM php:8.2-fpm AS builder

WORKDIR /var/www/onboardingapi

# Install system dependencies needed for building
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd xml zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel project
COPY . .

# Install PHP dependencies (production)
RUN composer install --no-dev --optimize-autoloader

# ===============================
# Stage 2: Production image
# ===============================
FROM php:8.2-fpm AS production

WORKDIR /var/www/onboardingapi

# Copy only necessary files from builder
COPY --from=builder /var/www/onboardingapi/vendor ./vendor
COPY --from=builder /var/www/onboardingapi/app ./app
COPY --from=builder /var/www/onboardingapi/bootstrap ./bootstrap
COPY --from=builder /var/www/onboardingapi/config ./config
COPY --from=builder /var/www/onboardingapi/database ./database
COPY --from=builder /var/www/onboardingapi/public ./public
COPY --from=builder /var/www/onboardingapi/routes ./routes
COPY --from=builder /var/www/onboardingapi/storage ./storage
COPY --from=builder /var/www/onboardingapi/artisan ./artisan

# Set permissions
RUN chown -R www-data:www-data /var/www/onboardingapi/storage /var/www/onboardingapi/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
