FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

ENV APP_ENV=prod

# Copy project into container
COPY . /var/www/html/

# Set working dir
WORKDIR /var/www/html/

# Ensure .htaccess works
RUN chown -R www-data:www-data /var/www/html