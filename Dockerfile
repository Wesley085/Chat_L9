FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurações do Laravel
WORKDIR /var/www
COPY . /var/www
RUN composer install && chmod -R 777 storage bootstrap/cache 

EXPOSE 9000
CMD ["php-fpm"]
