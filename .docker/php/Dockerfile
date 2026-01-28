FROM php:8.2-fpm

# Instalar herramientas y extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Carpeta de trabajo
WORKDIR /var/www

# Copiar todo el proyecto
COPY . /var/www

# Permisos correctos (muy importante)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Instalar dependencias (se ejecuta al hacer build)
RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm"]