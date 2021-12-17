FROM php:7.4-fpm

WORKDIR /var/www/application

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y curl git zip unzip libpq-dev libwebp-dev libzip-dev libpng-dev python3 libfreetype6-dev libjpeg62-turbo-dev procps sudo acl cron

RUN docker-php-ext-install pdo_mysql pdo_pgsql tokenizer exif zip && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    apt -y autoremove

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/hello-cron

# Create the log file to be able to run tail
# Run the command on container startup
CMD cron && touch /var/log/cron.log && tail -F /var/log/cron.log

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]