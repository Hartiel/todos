# Dockerfile
FROM php:8.2-fpm

# Instala extensões e dependências
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Instala dependências PHP
RUN composer install

# Define permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expõe a porta
EXPOSE 9000

CMD ["php-fpm"]
