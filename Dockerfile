FROM php:7.4-apache

# Instalamos dependencias y soporte para MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalamos Phalcon (Versión compatible con tu PHP 7.4)
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# REGLA DE ORO: Desactivar módulos que chocan
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# Copiamos tu proyecto
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80