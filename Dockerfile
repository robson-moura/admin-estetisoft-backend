FROM php:8.1-fpm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update && apt install -y \
    git unzip zip curl vim build-essential pkg-config \
    libzip-dev libicu-dev zlib1g-dev libxml2-dev libcurl4-openssl-dev libonig-dev \
    && docker-php-ext-install intl pdo_mysql zip bcmath mbstring xml opcache

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/backend

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader -vvv

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

ENV PORT=8080

EXPOSE 8080

CMD if [ "$MODE" = "railway" ]; then \
        php -S 0.0.0.0:$PORT -t public; \
    else \
        php-fpm; \
    fi
