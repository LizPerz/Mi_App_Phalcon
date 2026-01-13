FROM php:7.4-apache

# Instalamos lo básico y activamos errores para debug
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && echo "display_errors = On" > /usr/local/etc/php/conf.d/error-logging.ini

# Instalamos Phalcon
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# Forzamos la limpieza de módulos que chocan
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80