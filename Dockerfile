# Usamos PHP 7.4 estable con Apache
FROM php:7.4-apache

# Instalamos dependencias del sistema y la extensión zip
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

# Instalamos PSR y Phalcon via PECL
# Usamos versiones específicas que sabemos que funcionan bien en PHP 7.4
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# Habilitamos mod_rewrite (vital para las rutas de Phalcon)
RUN a2enmod rewrite

# Copiamos los archivos de tu proyecto
COPY . /var/www/html/

# Ajustamos permisos para que Apache pueda escribir en la caché
RUN chown -R www-data:www-data /var/www/html/cache

# Exponemos el puerto estándar
EXPOSE 80