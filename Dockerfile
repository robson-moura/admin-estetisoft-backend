FROM php:8.1-fpm

ENV DEBIAN_FRONTEND=noninteractive

# Instala dependências e extensões PHP
RUN apt update && apt install -y \
    git unzip zip curl vim build-essential pkg-config \
    libzip-dev libicu-dev zlib1g-dev libxml2-dev libcurl4-openssl-dev libonig-dev \
    && docker-php-ext-install intl pdo_mysql zip bcmath mbstring xml opcache

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/backend

# Copia todo o código (inclui bootstrap/app.php e artisan)
COPY . .

COPY . .

# Cria diretórios necessários com permissões antes do composer
RUN mkdir -p bootstrap/cache storage/framework storage/logs \
    && chmod -R 775 bootstrap/cache storage \
    && chown -R www-data:www-data bootstrap/cache storage

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Porta padrão para Railway
ENV PORT=9000
EXPOSE 9000

# Define o comando de execução baseado no ambiente
CMD if [ "$MODE" = "railway" ]; then \
        php -S 0.0.0.0:$PORT -t public; \
    else \
        php-fpm; \
    fi
