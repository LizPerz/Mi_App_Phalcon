# Usamos PHP 7.4 con Apache
FROM php:7.4-apache

# 1. Instalamos dependencias del sistema necesarias para compilar Phalcon
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install zip

# 2. Instalamos PSR (Requisito obligatorio para Phalcon 4+)
RUN pecl install psr && docker-php-ext-enable psr

# 3. Instalamos Phalcon mediante PECL (esto asegura que encuentre la versión correcta)
RUN pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# 4. Habilitamos mod_rewrite para que tus rutas de Phalcon funcionen
RUN a2enmod rewrite

# 5. Copiamos tu proyecto
COPY . /var/www/html/

# 6. Permisos para la caché y puerto
RUN chown -R www-data:www-data /var/www/html/cache
EXPOSE 80