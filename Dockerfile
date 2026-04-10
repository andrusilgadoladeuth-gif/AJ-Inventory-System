FROM php:8.2-apache

# Instalamos las extensiones de MySQL que PHP necesita
RUN docker-php-ext-install pdo pdo_mysql

# Habilitamos el módulo de reescritura de Apache (útil para el futuro)
RUN a2enmod rewrite