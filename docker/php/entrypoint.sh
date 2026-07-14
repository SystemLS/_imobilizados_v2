#!/bin/sh
set -e

echo "Preparando a aplicação Laravel..."

# Otimizações de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico do storage se não existir
if [ ! -d "/var/www/html/public/storage" ]; then
    php artisan storage:link
fi

# Aguardar o SQL Server (opcional, pode depender do ambiente)
# Vamos tentar rodar as migrations.
echo "Rodando migrations..."
php artisan migrate --force || echo "Atenção: Migrations falharam. O banco de dados pode não estar pronto."

# Executar o comando passado pro container (ex: php-fpm, queue, scheduler)
echo "Executando comando: $@"
exec "$@"
