# Laravel Todo API

A simple task management API built with Laravel. This application focuses on API authentication using Sanctum, supports refresh tokens, and is fully dockerized for easy setup and deployment.

---

## 🚀 Features

- User registration and login with token-based authentication (Sanctum)
- Access token and refresh token generation
- Full task CRUD
 -Validation and error handling
- Automated tests with PHPUnit
- Docker and Docker Compose for an easy development environment

---

## 🧰 Tecnologias

- PHP 8.2
- Laravel 10
- Sanctum (API authentication)
- MySQL (or other DB via Docker)
- Docker & Docker Compose

---

## 🐳 Running with Docker

### Prerequisites

- Docker installed
- Docker Compose

### Passo a passo

1. Clone the repository:

```bash
git clone https://github.com/seu-usuario/todo-api.git
cd todo-api
```

2. Copy the default .env file:

```bash
cp .env.example .env
```

3. Start the containers:

```bash
docker-compose up -d --build
```

4. Install Laravel dependencies:

```bash
docker exec -it laravel_app composer install
```

5. Generate app key and run migrations:

```bash
docker exec -it laravel_app php artisan key:generate
docker exec -it laravel_app php artisan migrate
```

The API will be available at: http://localhost:8000

## 🔐 Authentication

The API uses Laravel Sanctum for token-based authentication.

### Main Endpoints

- `POST /api/register` – User registration
- `POST /api/login` – Login and token generation
- `POST /api/refresh` – Generate new access token using refresh token
- Requires authentication:
- `GET /api/todos` – List tasks
- `POST /api/todos` – Create a new task
- `PUT /api/todos/{id}` – Update a task
- `DELETE /api/todos/{id}` – Delete a task

> For protected routes, send the token in the header:
`Authorization: Bearer {access_token}`

---

## 🧪 Tests

Tests are located in `tests/Feature/`.

To run them, use:

```bash
docker exec -it laravel_app php artisan test
```

## 📁 Key Structure

- app/Http/Controllers – API REST controllers
- routes/api.php – All exposed routes
- app/Models/Todo.php – Main task model
- database/factories/ – Factories for automated tests
- tests/Feature/ – Auth and task tests

## ✍️ Author

Built with 💻 by Arthur Reis

```bash
If you have any ideas for improvements or features to add, let me know ^^
```
