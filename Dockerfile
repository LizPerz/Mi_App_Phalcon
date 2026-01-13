FROM php:7.4-apache

# Instalamos dependencias básicas
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

# Instalamos Phalcon de forma manual para evitar conflictos de módulos
RUN pecl install psr && docker-php-ext-enable psr \
    && pecl install phalcon-4.1.2 && docker-php-ext-enable phalcon

# Esta línea es clave: limpia cualquier configuración previa de MPM que cause el error
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# Copiamos tu proyecto
COPY . /var/www/html/

# Permisos y puerto
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80