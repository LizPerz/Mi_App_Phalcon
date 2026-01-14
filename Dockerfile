FROM php:7.2-apache

# Instalamos dependencias para MySQL y Phalcon
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql

# Instalamos Phalcon 3.4.x (La versión exacta que pides)
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
    && apt-get install -y php7.2-phalcon3

# Activamos el módulo de reescritura para que funcionen las rutas
RUN a2enmod rewrite

# Copiamos tu proyecto
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80