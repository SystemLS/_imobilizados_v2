#!/bin/sh
set -e

echo "Preparando a aplicação Laravel..."

# Otimizações de configuração
php artisan config:cache || echo "Aviso: config:cache falhou"
php artisan route:cache || echo "Aviso: route:cache falhou"
php artisan view:cache || echo "Aviso: view:cache falhou"

# Criar link simbólico do storage se não existir
if [ ! -d "/var/www/html/public/storage" ]; then
    php artisan storage:link || echo "Aviso: storage:link falhou"
fi

# Aguardar o SQL Server (opcional, pode depender do ambiente)
# Vamos tentar rodar as migrations.
echo "Rodando migrations em background (para não travar o deploy)..."
nohup php artisan migrate --force > /var/log/migration.log 2>&1 &

# Substituir a porta do Nginx pela porta injetada pela Railway ($PORT)
if [ -n "$PORT" ]; then
    echo "Configurando Nginx para escutar na porta $PORT..."
    sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf
fi

# Garantir que a pasta de logs e pid do supervisor existam
mkdir -p /var/log/supervisor
mkdir -p /var/run/supervisor

# Executar o comando passado pro container (ex: supervisord)
echo "Executando comando: $@"
exec "$@"
