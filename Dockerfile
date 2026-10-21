FROM php:8.2-apache

# Install and enable the mysqli PHP extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files into the web root
COPY . /var/www/html/

# Configure permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80

CMD ["apache2-foreground"]
