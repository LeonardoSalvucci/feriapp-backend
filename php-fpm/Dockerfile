FROM php:7.3-fpm-alpine
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN mkdir /app
WORKDIR /app

RUN apk --no-cache add --virtual .build-deps $PHPIZE_DEPS \
  && apk --no-cache add --virtual .ext-deps libmcrypt-dev freetype-dev libzip-dev \
  libjpeg-turbo-dev libpng-dev libxml2-dev msmtp postgresql-dev \
  && docker-php-source extract \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
                                   --with-png-dir=/usr/include/ \
                                   --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd pdo pdo_mysql zip bcmath mbstring tokenizer xml ctype json opcache \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && docker-php-source delete \
  && apk del .build-deps \
  && apk --no-cache add curl git openssh \
  && curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
  
CMD ["composer", "install"]
CMD ["php-fpm"]