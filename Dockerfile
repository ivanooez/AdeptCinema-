# Production Dockerfile (optional)
FROM php:8.1-apache

# Enable apache mods
RUN a2enmod rewrite headers

# Install common extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip git curl \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install gd mysqli zip opcache

# Copy app
WORKDIR /var/www/html
COPY public/ /var/www/html/
COPY common/ /var/www/common/
COPY admin/ /var/www/admin/
COPY api/ /var/www/api/
COPY uploads/ /var/www/html/uploads/
COPY .env.example /var/www/html/.env.example

# Set permissions
RUN chown -R www-data:www-data /var/www/html/uploads || true
RUN chmod -R 755 /var/www/html/uploads || true

# Expose and run
EXPOSE 80
CMD ["apache2-foreground"]
