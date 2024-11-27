# Base Image
FROM php:8.4-fpm

# Arguments passed from docker-compose.yml
ARG WWWGROUP
ARG WWWUSER

# Set environment variables
ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    supervisor \
    gnupg \
    wget \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (Optional, for Vite or frontend builds)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# Install Xdebug (Optional, for debugging)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /var/www/html

# Copy application files (if building directly)
COPY . /var/www/html

# Set permissions
RUN groupadd --gid "$WWWGROUP" www && \
    useradd -G www --uid "$WWWUSER" --home /var/www/html --shell /bin/bash www && \
    chown -R www:www /var/www/html

# Install application dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose ports (default Laravel and Vite)
EXPOSE 80
EXPOSE 5173

# Start PHP-FPM
CMD ["php-fpm"]
