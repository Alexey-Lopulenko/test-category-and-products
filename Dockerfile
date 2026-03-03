FROM php:8.3-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# System deps + PDO MySQL
RUN apt-get update && apt-get install -y \
    git unzip \
 && docker-php-ext-install pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# Apache vhost
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Set DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html