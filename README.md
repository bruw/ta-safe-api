# TA-SAFE-API
API para o backend do sistema de rastreabilidade de celulares Tá Safe.

## Tecnologias

+ PHP 
+ Laravel Framework 
+ MySQL 
+ Docker

## Instalação Local

1. Requisitos

Instale o Docker Engine e o Docker Compose conforme a documentação oficial
- Docker Engine: https://docs.docker.com/engine/install/ubuntu/
- Docker Compose: https://docs.docker.com/compose/install/linux/

2. Clone o Repositório
~~~git
git clone git@github.com:bruw/ta-safe-api.git
~~~

3. Acesse a Pasta do Projeto
~~~bash
cd ta-safe-api
~~~

4. Instale as Dependências:

~~~bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
~~~

5. Iniciar o Ambiente Docker
~~~bash
./vendor/bin/sail up -d
~~~

6. Executar Migrations e Seeders
~~~bash
./vendor/bin/sail artisan migrate:refresh --seed
~~~

7. Copie o arquivo `example.env` e renomeie para `.env`
 
8. Gere uma APP_KEY
~~~bash
./vendor/bin/sail artisan key:generate
~~~

## Testes
Para executar os testes utilize o seguinte comando
~~~bash
./vendor/bin/sail artisan test
~~~


