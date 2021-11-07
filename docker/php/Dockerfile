FROM php:8.0-fpm-alpine

COPY config/php.ini /usr/local/etc/php/

RUN apk --update --no-cache add git

# Install modules
RUN apk add --no-cache \
    && docker-php-ext-install bcmath

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --version=2.1.11 --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

WORKDIR /var/www
CMD composer install ; php-fpm
EXPOSE 9000