FROM php:8.3-cli

RUN apt-get update \
  && apt-get install -y git unzip libzip-dev libicu-dev libonig-dev \
  && docker-php-ext-install pdo pdo_mysql intl zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# script de bootstrap
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8000
