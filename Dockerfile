FROM php:7.2-apache

# Cambiamos los repositorios a los archivos históricos de Debian para evitar el error 404
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list

# Instalamos dependencias para MySQL y Phalcon
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql

# Instalamos Phalcon 3.4.x
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
    && apt-get install -y php7.2-phalcon3

# Activamos el módulo de reescritura
RUN a2enmod rewrite

# Copiamos tu proyecto
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80