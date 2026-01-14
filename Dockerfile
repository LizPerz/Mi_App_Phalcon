FROM php:7.4-apache

# Instalamos dependencias y soporte para MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalamos Phalcon
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# SOLUCIÓN RADICAL AL MPM: 
# 1. Borramos todas las configuraciones de MPM activas para que no haya duplicados
# 2. Activamos manualmente solo el que necesitamos (prefork) y el rewrite
RUN rm -f /etc/apache2/mods-enabled/mpm_* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

RUN mkdir -p /var/www/html/cache && chmod -R 777 /var/www/html/cache
EXPOSE 80