# Usamos una imagen de PHP con Apache
FROM php:7.4-apache

# Instalamos dependencias necesarias para Phalcon
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install git

# Instalamos la extensión de Phalcon (Versión compatible con tu código)
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
    && apt-get install -y php7.4-phalcon

# Habilitamos mod_rewrite para que las rutas de Phalcon funcionen
RUN a2enmod rewrite

# Copiamos tu proyecto al servidor
COPY . /var/www/html/

# Ajustamos permisos
RUN chown -R www-data:www-data /var/www/html