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

# Define o diretório de trabalho para a raiz do projeto
WORKDIR /var/www/html

# Cria o arquivo .env diretamente no container
RUN echo "APP_ENV=production\nDB_CONNECTION=sqlite\nDB_DATABASE=/var/www/html/database.sqlite" > /var/www/html/.env

# Instala as dependências do projeto
RUN composer install

# Configura o DocumentRoot do Apache para o diretório public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Permissões para o diretório public
RUN chown -R www-data:www-data /var/www/html/public

# Permissões para o SQLite e outros arquivos na raiz
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta padrão do Apache
EXPOSE 80
