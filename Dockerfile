FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev \
    && docker-php-ext-install pdo_mysql

#Instalando extensão do redis para php
RUN apt-get update && apt-get install -y \
    libssl-dev \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho corretamente
WORKDIR /var/www/html/projetos/async-csv-import

# Permissões corretas para o Laravel
RUN chown -R www-data:www-data /var/www/html/projetos/async-csv-import

CMD ["php-fpm"]
