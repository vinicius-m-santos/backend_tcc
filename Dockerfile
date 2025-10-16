FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    gnupg \
    curl \
    && docker-php-ext-install pdo_pgsql intl zip opcache mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Optional: install netcat if you need wait-for-db scripts
RUN apt-get update && apt-get install -y netcat-openbsd && rm -rf /var/lib/apt/lists/*

# Install Xdebug
# RUN pecl install xdebug \
#     && docker-php-ext-enable xdebug

# Configure Xdebug
# COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY . .

COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --optimize-autoloader


# Ensure cache/log directories exist and are writable
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set default command
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]