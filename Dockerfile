# Imagem base oficial do PHP com Apache
FROM php:8.2-apache

# Instala extensões e dependências
RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    libzip-dev \
    zip \
    git \
    && docker-php-ext-install pdo pdo_sqlite

# Ativa o mod_rewrite do Apache (útil pra rotas amigáveis se precisar)
RUN a2enmod rewrite

# Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos da aplicação
COPY . /var/www/html

# Define o diretório de trabalho
WORKDIR /var/www/html

# Instala as dependências do projeto
RUN composer install

# Permissões para o SQLite funcionar
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta padrão do Apache
EXPOSE 80
