FROM php:8.4-fpm

WORKDIR /var/www/html

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm

# Limpar cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000

CMD ["php-fpm"]
