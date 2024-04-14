FROM php:7.3-fpm

WORKDIR /var/www/html

RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo_mysql gd mbstring exif pcntl bcmath opcache

    # Install additional PHP extensions
RUN docker-php-ext-install sockets

# Copy custom php.ini file
COPY ./php.ini /usr/local/etc/php/php.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

CMD ["php-fpm"]

EXPOSE 9000
