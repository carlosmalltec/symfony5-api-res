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
  docker exec -it symfony_php composer require symfony/orm-pack
  docker exec -it symfony_php composer require maker
  docker exec -it symfony_php composer require serializer




  1 - Criar uma entidade (entity) - A estrutura da tabela
  docker exec -it symfony_php bin/console make:entity

  2 - * antes é importante configurar o .env com os dados da conexao, o nome do banco informado nao precisa está criado, pode rodar docker exec -it bin/console doctrine:database:create
  DATABASE_URL=mysql://root:senha123@127.0.0.1:3306/baselab?serverVersion=5.7

  3 - Criar a migrate da entity para criar a tabela no banco
  docker exec -it symfony_php bin/console make:migration

  4 - depois criar a estrutura acima no banco (criar a tabela)
  docker exec -it symfony_php bin/console doctrine:migrations:migrate
  
  resultado com sucesso: "CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(150) NOT NULL, login VARCHAR(150) NOT NULL, senha VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, hash VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"


## criar controller para acessar a estrutura acima

1 - criar um controller 
docker exec -it symfony_php bin/console make:controller
- Informa o nome ex Usuario

2 - Depois de definir as routas pode usar o comando para listar todas criadas
docker exec -it symfony_php bin/console debug:router

saida 

  _preview_error   ANY         ANY      ANY    /_error/{code}.{_format}  
  usuario_index    GET         ANY      ANY    /usuario/                 
  usuario_view     GET         ANY      ANY    /usuario/{id}             
  usuario_create   POST        ANY      ANY    /usuario/{id}             
  usuario_update   PUT|PATCH   ANY      ANY    /usuario/{id}             
  usuario_delete   DELETE      ANY      ANY    /usuario/{id} 

### JWT

  docker exec -it symfony_php composer require security
  docker exec -it symfony_php composer require firebase/php-jwt

  1 - O proprio framework fornece um processo para criar o User
  docker exec -it symfony_php bin/console make:user
  docker exec -it symfony_php bin/console make:migration
  docker exec -it symfony_php bin/console doctrine:migrations:migrate
  
  resultado: 
  CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
     -> ALTER TABLE usuario CHANGE updated_at updated_at DATETIME DEFAULT NULL

  2 - criar dados de testes
  docker exec -it symfony_php composer require orm-fixtures
  docker exec -it symfony_php bin/console make:fixtures

  Para gerar senha pelo terminal
  docker exec -it symfony_php bin/console security:encode-password

  depois de criar os dados em UserDados

  rodar o comando para gravrar os dados no banco

  docker exec -it symfony_php bin/console doctrine:fixtures:load


  

