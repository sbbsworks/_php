FROM php:8.2-fpm
RUN rm /bin/sh && ln -s /bin/bash /bin/sh
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    libpq-dev \
    curl \
    htop \
    systemctl \
    zip \
    libssl-dev \
    wget \
    git \
    procps \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql pdo \
	&& pecl install xdebug-3.2.1 \
	&& docker-php-ext-enable xdebug

ENV NVM_DIR /tmp
ENV NODE_VERSION 21.5.0
RUN curl --silent -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.5/install.sh | bash \
  && . $NVM_DIR/nvm.sh \
  && nvm install $NODE_VERSION \
  && nvm alias default $NODE_VERSION \
  && nvm use default

RUN ln -s /$NVM_DIR/versions/node/$NODE_VERSION/bin/node /usr/bin/node
##ls -n /tmp/versions/node/v21.5.0/bin/node /usr/bin/node
RUN curl -fsSL https://get.pnpm.io/install.sh | env PNPM_VERSION=8.12.1 sh -


RUN pecl install swoole
RUN docker-php-ext-enable swoole

##############
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

CMD ["php-fpm"]