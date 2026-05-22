FROM php:8.2-apache

ENV PORT=10000

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/ports.conf /etc/apache2/ports.conf
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000

CMD ["apache2-foreground"]
