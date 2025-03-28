# API Async CSV Import

# Introdução

Projeto para importação assíncrona de arquivos CSV utilizando Laravel, Redis e Docker.

# Overview

A API permite:
    <br>
    <ul>
       <li>Cadastrar e autenticar via JWT um usuário administrador</li>
       <li>Fazer upload de um arquivo .csv com dados de usuários</li>
       <li>Cadastrar de forma assíncrona usuários em uma base de dados MySQL</li>
       <li>Obter os usuários cadastrados por paginação</li>
       <li>Consultar o status de uma importação específica</li>
       <li>Cadastrar logs das ações executadas nos principais endpoints</li>
    </ul>

# Configuração e Utilização

<h4>Pré-Requisitos:</h4>
   <ul>
      <li>PHP 8.2+</li>
      <li>Banco de dados MySQL 8.0+</li>
      <li>Laravel 8.83.29</li>
      <li>Redis: última versão</li>
      <li>Docker</li>
      <li>Docker compose</li>
   </ul>

<h4>Configurando ambiente</h4>
   <ul>
      <li>1. Acesse a pasta async-csv-import-app</li>
      <li>2. Configure (se necessário) o arquivo de ambiente .env com as credenciais do banco de dados e demais configurações</li>
      <li>3. Crie as imagens Docker
            <p>docker compose build</p>
      </li>
      <li>4. Inicie os containers Docker
            <p>docker compose up -d</p>
      </li>
      <li>5. Instale as dependências e configure o projeto
            <p>docker compose exec app bash</p>
            <p>composer install</p>
            <p>php artisan key:generate</p>
            <p>php artisan migrate</p>
            <p>php artisan storage:link</p>
      </li>
      <li>6. Verifique se todos os serviços estão rodando
            docker compose ps
      </li>
      <li>7. Deve haver quatro containers rodando
            - async-csv-import-app
            - async-csv-import-db
            - async-csv-import-redis
            - async-csv-import-queue
      </li>
   </ul>

<h4>Estrutura do Projeto</h4>

<ul>
    <li>app/Http/Controllers/AdminUserController.php: Responsável por validar e cadastrar um usuário administrador</li>
    <li>app/Http/Controllers/AuthController.php: Responsável por autenticar o usuário administrador</li>
    <li>app/Http/Controllers/UploadController.php: Responsável pelo upload dos arquivos CSV</li>
    <li>app/Jobs/ProcessCsvImport.php: Job que processa a importação assíncrona dos dados</li>
    <li>app/Services/CsvValidationService.php: Serviço para validação das entradas CSV<li>
    <li>app/Models/ImportStatus.php: Modelo para acompanhamento do status da importação
    <li>app/Models/User.php: Modelo para os usuários importados</li>
    <li>app/Models/ApiLog.php: Modelo para registrar os logs das ações feitas nos principais endpoints</li>
</ul>

<h4>Uso</h4>
   <ul>
      <li>Cadastrar um usuário admin com nome, usuário e senha. (POST /register)
      </li>
      <li>Efetuar login na aplicação (POST /login)
      </li>
      <li>Importar usuários: (POST /upload)
            <p>inicie o serviço de filas para criação e disparo do job de importação: docker exec -it async-csv-import-app php artisan queue:work --daemon</p>
            <p>utilizar o arquivo test.csv ou algum outro arquivo seguindo a mesma estrutura e extensão</p>
      </li>
      <li>Obter usuários cadastrados (GET /users)
      </li>
      <li>Consultar status da importação (GET /import-status/{id})
      </li>
      <li>Consultar logs da api na tabela api_logs na base de dados
      </li>
      <li>Logout (POST /logout)
      </li>
      <li>Obs: para acessar as rotas: upload de csv, obter usuários cadastrados e consultar status importação; é necessário estar autenticado via JWT.
      </li>
   </ul>

# Endpoints

#### Cadastro de usuário admin

```
POST /api/register
```

Body:

```json
{
  "name": "User Test",
  "user": "usertest",
  "password": "123"
}
```

Resposta:

```json
{
    "success": true,
    "message": "User admin created successfully"
}
```

#### Login

```
POST /api/login
```

Body:

```json
{
  "user": "usertest",
  "password": "123"
}
```

Resposta:

```json
{
    "success": true,
    "message": "Login successfully",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzQzMTYyNjA5LCJleHAiOjE3NDMxNjYyMDksIm5iZiI6MTc0MzE2MjYwOSwianRpIjoiblhOc1doODZFajZGUjFYOCIsInN1YiI6IjYiLCJwcnYiOiJjODI5MjIzODM1ZDExMTM4ZjA4YWNlNTZmZmE2NjI4YmMyNjgzY2I1In0.EfdtNpRnTcD1Uqilcdb1AGVupn-iCsF6J5G22n5Hl84",
        "token_type": "bearer"
    }
}
```

#### Upload de arquivo CSV

```
POST /api/upload
```

Parâmetros:

- `file`: Arquivo CSV (obrigatório)

Resposta:

```json
{
  "success": true,
  "message": "File successfully imported",
  "import_id": 1
}
```

#### Verificar status da importação

```
GET /api/import-status/{id}
```

Resposta:

```json
{
  "success": true,
  "message": "Import Status",
  "data": {
    "id": 1,
    "file_path": "uploads/csv_file.csv",
    "status": "completed",
    "description": "File successfully imported"
  }
}
```

#### Listar usuários

```
GET /api/users
```

Resposta:

```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "birthdate": "1990-01-01"
    },
    ...
  ],
  "links": {...},
  "meta": {...}
}
```

<h4>Testes</h4>
    A aplicação pode ser testada das seguintes formas:
<ul>
    <li>Postman: faça a importação da collection de rotas (CSV Import API.postman_collection) disponibilizada na raiz desse diretório e execute os endpoints na ordem apresentada na seção de Uso.
    </li>
    <li>Testes automatizados:
        <p>docker exec -it async-csv-import-app php artisan test</p>
    </li>
</ul>

# Repositório

O repositório desse projeto encontra-se em:

<https://github.com/wendreskennedy/api_async_csv_import>
