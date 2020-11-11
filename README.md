# SimpleRouter
### Sistema de rotas dinâmicas.
***
### Criar o arquivo .htaccess
> O arquivo ```.htaccess``` deve ser criado na pasta raiz.
```
RewriteEngine On
Options All -Indexes
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```
### Criar o index.php
![Criar Index](https://github.com/devceliojr/SimpleRouter/blob/main/img/CodePreview.png?raw=true)
