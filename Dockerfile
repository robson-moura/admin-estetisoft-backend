FROM php:8.1-fpm

# Instala dependências do sistema e extensões PHP
RUN apt update && apt install -y \
    git unzip zip curl vim \
    libzip-dev libicu-dev zlib1g-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install \
        intl \
        pdo \
        pdo_mysql \
        zip \
        bcmath \
        mbstring \
        tokenizer \
        xml \
        opcache

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/backend

# Copia o código do projeto
COPY . .

# Instala dependências PHP do Laravel
RUN composer install --no-dev --optimize-autoloader -vvv

# Ajusta permissões
RUN chown -R www-data:www-data storage bootstrap/cache


# Definir variável de porta (usada no Railway)
ENV PORT=8080

# Condicional: se ENV MODE=railway, usa php -S. Caso contrário, roda fpm (ex: para Nginx)
CMD if [ "$MODE" = "railway" ]; then \
        php -S 0.0.0.0:$PORT -t public; \
    else \
        php-fpm; \
    fi
