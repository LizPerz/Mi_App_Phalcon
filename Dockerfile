FROM php:7.4-apache

# Instalamos dependencias y el driver de MySQL para que tu BD funcione
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalamos Phalcon (v4.1.2 es la más estable para PHP 7.4)
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# ESTA LÍNEA ES LA MAGIA: Desactiva TODO lo que estorba y activa solo lo necesario
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork rewrite

# Copiamos archivos y damos permisos
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html/cache

EXPOSE 80