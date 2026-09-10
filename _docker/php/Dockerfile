FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
      wget \
      apt-utils \
      libpq-dev \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libzip-dev \
      zip unzip \
      git && \
      git clone --depth 1 --branch 3.5.1 https://github.com/xdebug/xdebug.git /tmp/xdebug && \
      cd /tmp/xdebug && phpize && ./configure && make && make install && \
      docker-php-ext-enable xdebug && \
      docker-php-ext-install pdo pdo_pgsql pgsql && \
      docker-php-ext-install bcmath && \
      docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
      docker-php-ext-install gd && \
      docker-php-ext-install zip && \
      docker-php-ext-install pcntl && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./_docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./_docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./_docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN usermod -u 1000 www-data
RUN chmod 777 -R /tmp && chmod o+t -R /tmp

WORKDIR /var/www

