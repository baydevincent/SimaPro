FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Create necessary directories if they don't exist
RUN mkdir -p /var/www/html/storage/{app,framework/{cache,sessions,views},logs} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create .env file if not exists
RUN if [ ! -f /var/www/html/.env ]; then cp /var/www/html/.env.docker /var/www/html/.env; fi

# Fix git ownership issue
RUN git config --global --add safe.directory /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer run-script post-autoload-dump

# Expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]
