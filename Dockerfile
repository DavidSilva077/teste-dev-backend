FROM php:8.4-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev

# Extensões PHP necessárias + Redis
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www

# Copiar arquivos para dentro do container
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www

# Instalar dependências PHP
RUN composer install

# Expor porta
EXPOSE 8000

# Comando padrão
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
