# Utilisez l'image PHP-FPM officielle comme base
FROM php:8.2-fpm

# Installez les dépendances Symfony (par exemple, Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install zip \
    intl \
    && docker-php-ext-enable intl \
    && rm -rf /var/lib/apt/lists/*

# Installez Composer globalement
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Node
RUN apt-get update && \
    apt-get install -y --no-install-recommends gnupg && \
    curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get update && \
    apt-get install -y --no-install-recommends nodejs
#    npm install npm-watch

# Activez les extensions PHP nécessaires pour Symfony (ajoutez d'autres au besoin)
RUN docker-php-ext-install pdo pdo_mysql

# Définissez le répertoire de travail
WORKDIR /var/www/html

# Copiez le code de votre application Symfony dans le conteneur
COPY . .

# Exposez le port PHP-FPM
EXPOSE 9000

# Commande de démarrage du conteneur
CMD ["php-fpm"]