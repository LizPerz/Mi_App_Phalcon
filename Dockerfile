FROM php:7.2-apache

# 1. Corregimos los repositorios para evitar errores de Debian antiguo
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list

# 2. Instalamos dependencias, librerías de PostgreSQL y los drivers necesarios
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 3. Instalamos Phalcon 3.4.5
RUN git clone --depth=1 -b v3.4.5 https://github.com/phalcon/cphalcon.git /tmp/cphalcon && \
    cd /tmp/cphalcon/build && \
    ./install && \
    docker-php-ext-enable phalcon && \
    rm -rf /tmp/cphalcon

# 4. Activamos el módulo de rutas y configuramos el proyecto
RUN a2enmod rewrite
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# 5. Puerto de salida
EXPOSE 80