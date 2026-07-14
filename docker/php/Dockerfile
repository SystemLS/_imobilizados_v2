# ==========================================
# STAGE 1: Composer (PHP Dependencies)
# ==========================================
FROM php:8.3-cli AS composer_stage
WORKDIR /app

# Instalar dependências de sistema para o Composer
RUN apt-get update && apt-get install -y git unzip zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar apenas os arquivos necessários para o composer install (para cache do Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copiar o restante da aplicação e gerar autoloader otimizado
COPY . .
RUN composer dump-autoload --optimize --no-dev


# ==========================================
# STAGE 2: Node (Vite/Assets)
# ==========================================
FROM node:20 AS node_stage
WORKDIR /app

# Copiar arquivos do package.json e instalar dependências
COPY package.json package-lock.json ./
RUN npm ci

# Copiar o restante da aplicação e buildar
COPY . .
RUN npm run build


# ==========================================
# STAGE 3: Final Image (PHP-FPM)
# ==========================================
FROM php:8.3-fpm AS final_stage

# Instalar pacotes de sistema necessários (gnupg, ca-certificates para MSSQL)
RUN apt-get update && apt-get install -y \
    gnupg2 \
    apt-transport-https \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    zip \
    unzip \
    curl \
    nano \
    && rm -rf /var/lib/apt/lists/*

# Adicionar repositório da Microsoft e instalar ODBC Driver
ENV ACCEPT_EULA=Y
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões do PHP
RUN docker-php-ext-install pdo mbstring zip exif pcntl bcmath gd intl opcache

# Instalar e habilitar sqlsrv e pdo_sqlsrv via PECL
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Configurar OPcache para produção
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar código-fonte da aplicação (do STAGE 1, já com dependências do Composer)
COPY --from=composer_stage /app /var/www/html

# Copiar os assets compilados pelo Vite (do STAGE 2)
COPY --from=node_stage /app/public/build /var/www/html/public/build

# Ajustar permissões para o usuário www-data (usuário padrão do PHP-FPM)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar script de inicialização
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Entrypoint e comando padrão
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
