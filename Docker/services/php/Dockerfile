FROM php:fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxpm-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql gd mbstring zip

    # Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer les dépendances PHP (y compris dompdf via Composer)
RUN curl -sS https://getcomposer.org/installer | php

# Exposer le port 9000 (pour PHP-FPM)
EXPOSE 9000