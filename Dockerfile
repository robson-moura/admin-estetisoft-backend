FROM php:8.1-fpm

# Atualizar pacotes e instalar dependências necessárias
RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev unzip curl vim \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar o diretório de trabalho
WORKDIR /var/www/backend

# Copia arquivos do projeto (melhore isso com .dockerignore)
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões para storage e cache
RUN chmod -R 775 storage bootstrap/cache

# Definir variável de porta (usada no Railway)
ENV PORT=8080

# Condicional: se ENV MODE=railway, usa php -S. Caso contrário, roda fpm (ex: para Nginx)
CMD if [ "$MODE" = "railway" ]; then \
        php -S 0.0.0.0:$PORT -t public; \
    else \
        php-fpm; \
    fi
