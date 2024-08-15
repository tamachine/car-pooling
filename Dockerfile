FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    git \
    supervisor \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_sqlite zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*    

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

WORKDIR /var/www/html

RUN touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite

RUN composer install

RUN php artisan migrate

COPY ./supervisor/supervisord.conf /etc/supervisor/supervisord.conf

RUN echo "Listen 9091" >> /etc/apache2/ports.conf

EXPOSE 9091

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]

