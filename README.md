# Laravel Todo API

API simples de gerenciamento de tarefas desenvolvida com Laravel. Esta aplicação foi criada com foco em autenticação via API usando Sanctum, suporte a refresh tokens e totalmente dockerizada para facilitar o setup e deploy.

---

## 🚀 Funcionalidades

- Registro e login de usuários com autenticação via token (Sanctum)
- Geração de **access token** e **refresh token**
- CRUD completo de tarefas
- Validação e tratamento de erros
- Testes automatizados com PHPUnit
- Docker e Docker Compose para facilitar o ambiente de desenvolvimento

---

## 🧰 Tecnologias

- PHP 8.2
- Laravel 10
- Sanctum (autenticação via API)
- MySQL (ou outro banco via Docker)
- Docker & Docker Compose

---

## 🐳 Como rodar com Docker

### Pré-requisitos

- Docker instalado
- Docker Compose

### Passo a passo

1. Clone o repositório:

```bash
git clone https://github.com/seu-usuario/todo-api.git
cd todo-api
```

2. Copie o .env padrão:

```bash
cp .env.example .env
```

3. Suba os containers:

```bash
docker-compose up -d --build
```

4. Instale as dependências do Laravel:

```bash
docker exec -it laravel_app composer install
```

5. Gere a chave da aplicação e rode as migrations:

```bash
docker exec -it laravel_app php artisan key:generate
docker exec -it laravel_app php artisan migrate
```

A API estará disponível em: http://localhost:8000

## 🔐 Autenticação

A API usa **Laravel Sanctum** para autenticação via tokens.

### Endpoints principais

- `POST /api/register` – Registro de usuário
- `POST /api/login` – Login e geração de tokens
- `POST /api/refresh` – Gera novo access token a partir do refresh token
- Requer autenticação:
- `GET /api/todos` – Lista as tarefas
- `POST /api/todos` – Cria uma nova tarefa
- `PUT /api/todos/{id}` – Atualiza uma tarefa
- `DELETE /api/todos/{id}` – Deleta uma tarefa

> Para rotas protegidas, envie o token no cabeçalho:  
`Authorization: Bearer {access_token}`

---

## 🧪 Testes

Os testes estão localizados em `tests/Feature/`.

Para rodá-los, utilize:

```bash
docker exec -it laravel_app php artisan test
```

## 📁 Estrutura importante

- app/Http/Controllers – Controladores REST da API
- routes/api.php – Todas as rotas expostas
- app/Models/Todo.php – Modelo principal da tarefa
- database/factories/ – Factories para testes automatizados
- tests/Feature/ – Testes de autenticação e tarefas

## ✍️ Autor

Feito com 💻 por Arthur Reis

```bash
Se quiser colocar exemplos de request/resposta ou tokens JWT, posso adicionar também. Só pedir!
```