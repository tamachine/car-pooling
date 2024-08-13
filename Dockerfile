FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_sqlite zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install

RUN php artisan migrate

RUN vendor/bin/phpunit --configuration /var/www/html/phpunit.xml

EXPOSE 9091

CMD ["sh", "-c", "php /var/www/html/artisan queue:work --sleep=3 --tries=3 & apache2-foreground"]
