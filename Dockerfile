# Dockerfile
FROM php:8.2-apache

# Instal·lar extensions necessàries per PDO i MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite (útil per projectes PHP)
RUN a2enmod rewrite

# Directori de treball dins el contenidor
WORKDIR /var/www/html

# Copiar el contingut de la carpeta src (en mode build)
COPY ./src /var/www/html
