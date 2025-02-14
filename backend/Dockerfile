FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    rabbitmq-c-dev \
    $PHPIZE_DEPS \
    git

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip \
    && pecl install redis amqp \
    && docker-php-ext-enable redis amqp

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --no-dev

# Copy the rest of the application
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize

# Install PHP development server
RUN apk add --no-cache php-cli

# Expose port 8000
EXPOSE 8000

# Start PHP development server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
