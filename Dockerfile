## Using PHP 7.4.6 Apache
FROM php:7.4-apache
## Install drivers
RUN apt-get update \
    && apt-get install -y nano apt-utils curl supervisor cron zip \
    && docker-php-ext-install mysqli pdo pdo_mysql opcache
## Install Composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -f /tmp/composer-setup.*
## Set working directory
WORKDIR /var/www/html
# Install dependencies
COPY composer.json composer.lock ./
RUN composer update --no-interaction --no-dev --no-scripts --no-autoloader
## Copy the app virtual host configuration
COPY hrms-apache.conf /etc/apache2/sites-available
## Copy the Queue worker configurations
COPY hrms-worker.conf /etc/supervisor/conf.d/hrms-worker.conf
# Copy the crontab entry script
COPY crontab /etc/crontabs/root
# Copy the entrypoint script
COPY entry-point.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entry-point.sh
## Create the .env file
COPY .env.example .env
## Copy everything else to working directory
COPY . .
## Run the post-install scripts manually
RUN composer dump-autoload
## Boot the APP
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod 755 /var/www/html/
## Set up supervisor
## Start the supervisor queue worker
RUN service supervisor start \
    && supervisorctl reread \
    && supervisorctl update \
    && supervisorctl start hrms-worker:*
## Disable default site and enable the app site
RUN a2dissite 000-default.conf \
    && a2ensite hrms-apache.conf \
    && a2enmod rewrite \
    && service apache2 restart
# Set entrypoint
ENTRYPOINT ["entry-point.sh"]
CMD ["apachectl","-D", "FOREGROUND"]
## Expose the port for use outside the container to access the app
EXPOSE 80
