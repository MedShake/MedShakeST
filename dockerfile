# Utilise la dernière image PHP avec Apache
FROM php:apache

# Installe les outils nécessaires et les extensions PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libyaml-dev \
    gettext \  
    unzip \
    git \
    && pecl install yaml \  
    && docker-php-ext-enable yaml \  
    && docker-php-ext-install pdo pdo_mysql intl zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable intl

# Active le module Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache pour autoriser les fichiers .htaccess dans /public_html
COPY docker-vhost.conf /etc/apache2/sites-available/000-default.conf

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie les fichiers du projet
COPY . /var/www

# Exécute composer update en racine
WORKDIR /var/www
RUN composer install --ignore-platform-reqs --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --no-dev 

# Exécute composer update dans /public_html
WORKDIR /var/www/public_html
RUN composer install --ignore-platform-reqs --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --no-dev 

# Génère config.yml à partir du template en remplaçant les variables d'environnement
WORKDIR /var/www
COPY config/config.docker-template.yml /var/www/config/config.yml

# Permissions pour Apache
RUN chown -R www-data:www-data /var/www
