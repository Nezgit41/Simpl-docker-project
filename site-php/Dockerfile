FROM php:8-fpm
ENV APP_HOME /var/www
ENV USERNAME=www-data
ARG HOST_UID=1000
ARG HOST_GID=1000

RUN apt-get update && apt-get install -y \
    curl \
    git \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    libmcrypt-dev \
    nano

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install mysqli zip pdo pdo_mysql \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
#    && docker-php-ext-configure intl


# create document root, fix permissions for www-data user and change owner to www-data
RUN mkdir -p $APP_HOME/public && \
    mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
    && groupmod -o -g $HOST_GID $USERNAME \
    && chown -R ${USERNAME}:${USERNAME} $APP_HOME

# put php config for Laravel
COPY ./site-php/nginx/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./site-php/nginx/php.ini /usr/local/etc/php/php.ini

#RUN docker-php-ext-install
#RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/

# Copy existing application directory contents
#COPY . .




#ADD ./docker/php/php.ini /usr/local/etc/php/php.ini

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# set working directory
WORKDIR $APP_HOME

# Change current user
USER ${USERNAME}

# copy source files and config file
COPY --chown=${USERNAME}:${USERNAME} ./site-php $APP_HOME/
#COPY --chown=${USERNAME}:${USERNAME} ./site-php/.env.production $APP_HOME/.env


USER root
# Expose port 9000 and start php-fpm server
EXPOSE 9000
#
#CMD ["php-fpm"]
#; php artisan migrate:refresh
