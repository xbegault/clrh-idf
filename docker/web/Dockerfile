FROM php:5.6-apache

RUN apt-get update && \
 apt-get install -y vim nano wget zlib1g zlib1g-dev && \
 docker-php-ext-install pdo_mysql mysqli

#ADD 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod headers
RUN a2enmod rewrite

#RUN echo "memory_limit = 1024M\n" \
#            > /usr/local/etc/php/conf.d/uploads.ini
