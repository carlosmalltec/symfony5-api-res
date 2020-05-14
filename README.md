# rest-api-symfony
Cadastro de usuário - Tecnologias (Symfony 5.1, MariaDB, MongoDB, Doctrine, Redis e GraphQL).


rascunho

1 - docker-compose up --build
2 - docker exec -it baselab_php composer create-project symfony/skeleton nome_do_projeto
ou
servidor do php docker exec -it baselab_php php -S localhost:8080 -t public
php 7.4
mariaDB 10.4
REDIS_VERSION=6.0.1
mongo "BITNAMI_IMAGE_VERSION=4.0.14-debian-9-r30",
NGINX_VERSION=1.17.10

Creating a "symfony/skeleton" project at "./apirest"
Installing symfony/skeleton (v5.0.99)
  - Installing symfony/skeleton (v5.0.99): Downloading (100%) 

  pacotes:

  docker exec -it symfony_php composer require annotation
  docker exec -it symfony_php composer require orm-pack
  docker exec -it symfony_php composer require maker