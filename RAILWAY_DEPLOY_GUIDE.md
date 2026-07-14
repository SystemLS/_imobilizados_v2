# Guia de Implantação na Railway (Docker + SQL Server)

Agora que o repositório está no GitHub com os arquivos Docker, o processo de subir para a Railway é automatizado. Siga o passo a passo abaixo para colocar a sua aplicação e o banco de dados Microsoft SQL Server no ar.

## Passo 1: Importar o Projeto na Railway
1. Acesse o dashboard da [Railway.app](https://railway.app/).
2. Clique no botão **New Project** (Novo Projeto).
3. Selecione a opção **Deploy from GitHub repo**.
4. Conceda acesso à sua conta e selecione o repositório recém-criado: `SystemLS/_imobilizados_v2`.
5. Como nós incluímos o arquivo `docker-compose.yml`, a Railway detectará automaticamente que há vários serviços. Aceite a criação múltipla e aguarde o primeiro deploy. *(Nota: O primeiro deploy de cada container deve falhar no início porque as variáveis de ambiente ainda não foram configuradas. Isso é normal).*

---

## Passo 2: Configurar o SQL Server
Na Railway, o Microsoft SQL Server será hospedado através de um dos containers (`sqlserver`) declarados no `docker-compose.yml`.

1. No canvas do seu projeto na Railway, clique no serviço correspondente ao **sqlserver**.
2. Vá na aba **Variables**.
3. Adicione a seguinte variável (que você mesmo definirá para proteger o banco):
   - `DB_PASSWORD` = *UmaSenhaSua123!* (Certifique-se de que tenha mais de 8 caracteres, números e letras para atender aos requisitos da Microsoft).
4. O container do banco vai reiniciar e ficar pronto!

---

## Passo 3: Configurar as Variáveis da Aplicação Laravel
Agora vamos conectar o servidor Web (App, Queue e Scheduler) ao banco de dados e dar vida à aplicação.

1. No painel da Railway, clique num botão superior direito chamado **"Shared Variables"** (Variáveis Compartilhadas). Variáveis colocadas aqui serão aplicadas a todos os containers do Laravel (App, Queue e Scheduler) ao mesmo tempo, poupando trabalho.
2. Adicione as seguintes variáveis obrigatórias:
   - `APP_KEY` = *(copie o valor da variável APP_KEY que está no arquivo `.env` do seu PC local)*
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `LOG_CHANNEL` = `stderr` *(Isso é muito importante para os logs aparecerem no painel da Railway)*
   - `DB_CONNECTION` = `sqlsrv`
   - `DB_HOST` = `sqlserver` *(nome do serviço do banco de dados definido no compose)*
   - `DB_PORT` = `1433`
   - `DB_DATABASE` = `GestaoImobilizado1`
   - `DB_USERNAME` = `sa`
   - `DB_PASSWORD` = *(A mesma senha forte que você colocou no Passo 2)*
3. Clique em **Apply** / Salvar. Isso fará com que o painel reinicie os containers.

---

## Passo 4: Expor para a Internet
Até agora, a aplicação está rodando na infraestrutura fechada, mas não tem uma URL (link) para o público acessar.

1. Clique no serviço **nginx** (que é o servidor Web encarregado de direcionar o tráfego público para a sua aplicação).
2. Vá até a aba **Settings** (Configurações).
3. Na seção "Networking" (Rede), clique no botão **Generate Domain** (Gerar Domínio) ou adicione um domínio customizado (Custom Domain) se você tiver um.
4. Na mesma aba, verifique se a porta (`PORT`) mapeada está configurada como `80`.

Pronto! Acesse o link gerado pela Railway. O `entrypoint.sh` configurado no Docker rodará o cache da aplicação e garantirá que as tabelas do banco de dados (Migrations) sejam criadas automaticamente no instante em que o Laravel ligar!
