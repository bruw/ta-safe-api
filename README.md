# TA-SAFE-API
API para o backend do sistema de rastreabilidade de celulares - Tá Safe.

## Tecnologias

+ PHP 
+ Laravel Framework 
+ MySQL 
+ Docker

## Instalação Local

### Requisitos
- Docker Engine
- Docker Compose

### Passos

1. Clone o Repositório
~~~git
git clone git@github.com:bruw/ta-safe-api.git
~~~

2. Acesse a Pasta do Projeto
~~~bash
cd ta-safe-api
~~~

3. Instale as Dependências:

~~~bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
~~~

4. Copie o arquivo `example.env` e renomeie para `.env`

5. Modifique o `.env` com suas variáveis
~~~bash
DB_DATABASE=ta_safe_api
DB_USERNAME=root
DB_PASSWORD=root
~~~

6. Iniciar o Ambiente Docker
~~~bash
./vendor/bin/sail up -d
~~~

7. Gere uma APP_KEY
~~~bash
./vendor/bin/sail artisan key:generate
~~~

8. Executar Migrations e Seeders
~~~bash
./vendor/bin/sail artisan migrate:refresh --seed
~~~

## Testes
Para executar os testes utilize o seguinte comando
~~~bash
./vendor/bin/sail artisan test
~~~


