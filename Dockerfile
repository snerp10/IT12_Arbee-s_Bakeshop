# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for PostgreSQL and other extensions
RUN apt-get update && \
    apt-get install -y libpq-dev git zip unzip && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Enable Apache mod_rewrite (for Laravel routing)
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Give permissions to storage and cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 8080 for Render
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]