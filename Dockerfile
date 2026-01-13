# Usamos PHP 7.4 con Apache
FROM php:7.4-apache

# Instalamos herramientas del sistema (incluyendo git)
RUN apt-get update && apt-get install -y \
    curl \
    libzip-dev \
    zip \
    unzip \
    git \
    gnupg \
    && docker-php-ext-install zip

# Instalamos la extensión de Phalcon para PHP 7.4
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
    && apt-get install -y php7.4-phalcon

# Habilitamos mod_rewrite para que las rutas de Phalcon funcionen
RUN a2enmod rewrite

# Copiamos tu proyecto al servidor de Railway
COPY . /var/www/html/

# Damos permisos a la carpeta de caché para que Phalcon pueda escribir
RUN chown -R www-data:www-data /var/www/html/cache

# Puerto estándar
EXPOSE 80