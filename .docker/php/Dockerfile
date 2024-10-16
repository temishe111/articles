# Use the official PHP 8.3.1-FPM image as the base image
FROM php:8.3.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libxslt1-dev \
    libgmp-dev \
    libldap2-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_pgsql \
    zip \
    gd \
    opcache \
    mbstring \
    exif \
    pcntl \
    bcmath \
    calendar \
    soap \
    xsl \
    gmp \
    ldap \
    sockets

# Install APCu
RUN pecl install apcu && docker-php-ext-enable apcu

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 9000 and start PHP-FPM
EXPOSE 9001
CMD ["php-fpm"]

# Healthcheck to ensure the PHP-FPM service is running
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s CMD curl --fail http://localhost:9001 || exit 1
