## Using PHP 7.4.6 Apache
FROM php:apache
## Install drivers
RUN apt-get update \
    && apt-get install -y nano apt-utils curl zip zlib1g-dev libzip-dev nodejs git libpng-dev supervisor cron \
    && docker-php-ext-install mysqli pdo_mysql mbstring opcache zip gd
## Install Composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -f /tmp/composer-setup.*
## Copy the app virtual host configuration
COPY hrms-apache.conf /etc/apache2/sites-available
## Copy the Queue worker configurations
COPY hrms-worker.conf /etc/supervisor/conf.d/hrms-worker.conf
## Set working directory
WORKDIR /var/www/html
## Create the .env file
COPY .env.example .env
## Copy everything else to working directory
COPY . .
## Boot the APP
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod 755 /var/www/html/ \
    #&& chmod -R 775 storage \
    && composer install --no-interaction --no-dev
## Set up supervisor
## Start the supervisor queue worker
RUN service supervisor start \
    && supervisorctl reread \
    && supervisorctl update \
    && supervisorctl start hrms-worker:*
## Make crontab entry
RUN crontab -l | { cat; echo "* * * * * cd /var/www/html/ && php artisan schedule:run >> /dev/null 2>&1 \n"; } | crontab -
## Disable default site and enable the app site
RUN a2dissite 000-default.conf \
    && a2ensite hrms-apache.conf \
    && a2enmod rewrite \
    && service apache2 restart
## Expose the port for use outside the container to access the app
EXPOSE 80
