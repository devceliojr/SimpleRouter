# SimpleRouter
## Sistema de rotas dinâmicas.
___

### Criar o arquivo .htaccess
> O arquivo ```.htaccess`` deve ser criado na pasta raiz.
```regex
RewriteEngine On
Options All -Indexes
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```
___

## Namespace
> o método **_namespace()_** é geralmente associado ao padrão [MVC](https://pt.wikipedia.org/wiki/MVC) e define
> onde o determinada rota fará a busca por determinada classe.
>

#### Exemplo:

```
$route->namespace('App/Controllers');
```
___

## Group
> o método **_group()_** define a rota **PAI** e tudo que vier após a definição
> será considerada uma rota **FILHA**.
>

#### Exemplo:
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$route = new \SimpleRouter\Application\Router();

$route->namespace('App/Controllers');

$route->group('/users');
$route->get('/all', 'Controller@method');
$route->get('/{id}/profile', 'Controller@method');

$route->dispatch();

var_dump($route);
```
#### Output:

```php
SimpleRouter\Application\Router: (object) [Object ID #3][2 properties]
    routes: (array) [1 element]
        GET: (array) [2 elements]
            /users/all: (string) "Controller@method"
            /users/{id}/profile: (string) "Controller@method"
```
___

## Rotas

### Declaração

###### Rotas estáticas

> Basicamente é uma rota que não receberá parâmetros.

```php
<?php
# Autoload.
require_once __DIR__ . '/vendor/autoload.php';

# Instância.
$route = new \SimpleRouter\Application\Router();

# Definição do namespace onde serão buscadas as classes.
$route->namespace('App/Controllers');

# Declarando um grupo de rotas, o exemplo abaixo é mesmo que declarar null.
$route->group();

# Rota com verbo GET, os verbos suportados são: GET, POST, PUT, PATCH e DELETE.
$route->get('/', 'Controller@method');

# Executando as rotas.
$route->dispatch();
```

###### Rotas dinâmicas

> Ao contrário da anterior, está rora receberá parâmetros que podem ser recuperados
> posteriormente.

```php
<?php
# Autoload.
require_once __DIR__ . '/vendor/autoload.php';

# Instância.
$route = new \SimpleRouter\Application\Router();

# Definição do namespace onde serão buscadas as classes.
$route->namespace('App/Controllers');

# Declarando um grupo de rotas, o exemplo abaixo é mesmo que declarar null.
$route->group();

# Rota dinâmica.
# Os dados podem ser recuperados através do método getParameters().
$route->get('/{name}/{age}', 'Controller@method');

# Executando as rotas.
$route->dispatch();
```

###### Rotas com callback

> É possível (E muito simples) definir uma função de retorno para o **_VERBO HTTP_**
> que você desejar!
```php
<?php
# Autoload.
require_once __DIR__ . '/vendor/autoload.php';

# Instância.
$route = new \SimpleRouter\Application\Router();

# Definição do namespace onde serão buscadas as classes.
$route->namespace('App/Controllers');

# Declarando um grupo de rotas, o exemplo abaixo é mesmo que declarar null.
$route->group();

# Rota com callback.
$route->post('/', function($data) {
    echo "<p>His name is <strong>{$data->name}</strong> and his age is <strong>{$data->age}</strong> years.</p>";
});

# Executando as rotas.
$route->dispatch();
```
___

## Redirecionamento
```php
<?php
# Autoload.
require_once __DIR__ . '/vendor/autoload.php';

# Instância.
$route = new \SimpleRouter\Application\Router();

# Definição do namespace onde serão buscadas as classes.
$route->namespace('App/Controllers');

# Declarando um grupo de rotas, o exemplo abaixo é mesmo que declarar null.
$route->group();

# Redirecionando a rota para '/redirect-page'.
$route->redirect('/redirect-page');

# Executando as rotas.
$route->dispatch();
```

___

## Tratando erros nas rotas

> O tratamento dos erros pode ser muito útil na criação de uma classe ou função
> que se responsabilizará por direcionar o usuário em caso de erro na rota.

```php
<?php
# Autoload.
require_once __DIR__ . '/vendor/autoload.php';

# Instância.
$route = new \SimpleRouter\Application\Router();

# Definição do namespace onde serão buscadas as classes.
$route->namespace('App/Controllers');

# Declarando um grupo de rotas, o exemplo abaixo é mesmo que declarar null.
$route->group();

# Rota estática.
$route->get('/', 'Controller@method');

# Definindo um novo grupo de rotas
$route->group('/erro');
$route->get('/{errcode}', 'Error@index');

# Executando as rotas.
$route->dispatch();

# Tratamento de erro
if (!is_null($route->error())) {
    $route->redirect("/erro/{$route->error()}");
}
```

___

## Códigos de erro

- ### 400
  - Bad Request.
    - *Indica que o servidor não pode ou não irá processar a requisição devido a alguma coisa que foi entendida como um erro do cliente (por exemplo, sintaxe de requisição mal formada, enquadramento de mensagem de requisição inválida ou requisição de roteamento enganosa).*

- ### 404
    - Not Found.
        - *Indica que o servidor não conseguiu encontrar o recurso solicitado. Normalmente, links que levam para uma página 404 estão quebrados ou desativados, e podem estar sujeitos a link rot.*

- ### 405
    - Method Not Allowed.
        - *Indica que o verbo HTTP utilizado não é suportado, por exemplo: a requisição ocorre por meio de um GET, porém o único método disponível é o POST.*

- ### 501
    - Not Implemented.
        - *Indica que o servidor não suporta a funcionalidade requerida para completar a requisição.*
    
