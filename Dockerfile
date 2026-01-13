
FROM php:7.4-apache

# 1. Instalamos dependencias y zip
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

# 2. Instalamos PSR y Phalcon (esto tarda un poquito, es normal)
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# 3. SOLUCIÓN AL ERROR MPM: Desactivamos el módulo conflictivo y activamos el correcto
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# 4. Copiamos tu proyecto
COPY . /var/www/html/

# 5. Permisos finales
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
