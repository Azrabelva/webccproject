# Base image PHP + Apache
FROM php:8.2-apache

# Enable Apache rewrite (penting untuk routing)
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy source code ke container
COPY . /var/www/html

# Set permission (penting di Azure)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
