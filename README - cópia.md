# rest-api-symfony
Cadastro de usuário - Tecnologias (Symfony 5.1, MariaDB, MongoDB, Doctrine, Redis e GraphQL).

ATENÇÃO 

ALTERAR .ENV
DATABASE_URL=mysql://root:senha123@127.0.0.1:3306/baselab?serverVersion=5.7

docker exec -it symfony_php bin/console make:fixtures

http://localhost/usuario/login - POST
{
  "username":"carlos",
  "password":"123456"
}

http://localhost/usuario/index - GET
CABEÇALHO Authorization Bearer XXXXXXXXX

http://localhost/usuario/view/ID - GET
CABEÇALHO Authorization Bearer XXXXXXXXX

http://localhost/usuario/create - POST
CABEÇALHO Authorization Bearer XXXXXXXXX
{
  "username":"joseSSS",
  "password": "123"
}

// o envio de com os dados para recuperar
http://localhost/usuario/recuperar - POST
CABEÇALHO Authorization Bearer XXXXXXXXX

 {
  "username":"carlos"
}


http://localhost/produto/ - POST

{
  "titulo":"Sacola",
  "descricao": "Sacola com faixas pretas e suporta até 2kilos",
  "preco": "10.99"
}

http://localhost/produto/ - GET

http://localhost/produto/1 - GET

http://localhost/produto/1 - DELETE

http://localhost/produto/1 - PUT
{
  "titulo":"Sacola do Zé",
  "descricao": "Sacola com faixas pretas e suporta até 2kilos",
  "preco": "16.99"
}
