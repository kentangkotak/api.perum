FROM php:8.2-fpm

# Arguments from docker-compose.yml
ARG user
ARG uid

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    default-mysql-client \
    supervisor \
    && docker-php-ext-install \
       pdo \
       pdo_mysql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
       zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Laravel app
COPY . .

RUN composer require beyondcode/laravel-websockets:^1.14 -W \
    && php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

# Set permissions
RUN chown -R ${user}:${user} /var/www \
    && chmod -R 755 /var/www

RUN chown -R ${user}:${user} /var/www
USER ${user}


EXPOSE 9000

CMD ["php-fpm"]
