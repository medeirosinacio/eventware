FROM php:8.4-fpm

WORKDIR /app

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

# Configurar Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug; \
     echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/99_php.ini; \
     echo "opcache.interned_strings_buffer=72" >> /usr/local/etc/php/conf.d/99_php.ini; \
     echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/50_xdebug.ini; \
     echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/50_xdebug.ini; \
     echo "zend_extension=xdebug" >> /usr/local/etc/php/conf.d/50_xdebug.ini;

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000

CMD ["php-fpm"]
