FROM php:8.1-fpm

# Evita prompts interativos no apt
ENV DEBIAN_FRONTEND=noninteractive

# Instala dependências do sistema e extensões PHP necessárias
RUN apt update && apt install -y \
    git unzip zip curl vim \
    libzip-dev libicu-dev zlib1g-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip bcmath mbstring xml opcache

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/backend

# Copia os arquivos de dependências e instala para aproveitar cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader -vvv

# Copia o restante do código
COPY . .

# Ajusta permissões
RUN chown -R www-data:www-data storage bootstrap/cache

# Define variável de ambiente para a porta (Railway)
ENV PORT=8080

EXPOSE 8080

# Comando para rodar dependendo do ambiente
CMD if [ "$MODE" = "railway" ]; then \
        php -S 0.0.0.0:$PORT -t public; \
    else \
        php-fpm; \
    fi
