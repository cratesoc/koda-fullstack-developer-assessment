# Task Manager

A simple task management application built with **Laravel**, **Vue 3**, **Tailwind CSS**, and **SQLite**.

## Features

* User registration and login
* Session-based authentication using Laravel Sanctum
* Create tasks
* View tasks
* Edit tasks
* Delete tasks
* Mark tasks as completed
* Tasks are associated with the authenticated user
* User information displayed in the task list
* Protected task API endpoints

## Tech Stack

* **OS:** Linux
* **Backend:** Laravel 13
* **Frontend:** Vue 3
* **CSS:** Tailwind CSS
* **Database:** SQLite
* **Authentication:** Laravel Sanctum
* **Build Tool:** Vite
* **Package Manager:** npm
* **PHP Dependency Manager:** Composer

---

## Requirements

**Target OS:** The application is built and intended to run natively on a **Linux** environment.
* **Development Environment:** Development and initial testing were conducted using **WSL2 (Windows Subsystem for Linux) - Ubuntu 26.04.1 LTS**. 
* **Pathing & Commands:** All file paths, shell scripts, and build commands assume a POSIX-compliant environment (Linux/macOS) and may require adjustment if executed natively on Windows (PowerShell/CMD).

Install the following:

* PHP 8.5
* PHP SQLite extension
* Composer 2
* Node.js 22
* npm 9

The easiest way to install the required PHP development stack is using php.new:

```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After the installation completes, restart your terminal or reload your shell configuration.

Install Nodejs and NPM:

```bash
sudo apt update
sudo apt install nodejs npm
```

Check the installed versions:

```bash
php -v
composer -V
node -v
npm -v
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/cratesoc/koda-fullstack-developer-assessment.git
cd koda-fullstack-developer-assessment
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Create the environment file

```bash
cp .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

---

## SQLite Database

This application uses **SQLite**, so no MySQL or MariaDB server is required.

Create the SQLite database:

```bash
touch database/database.sqlite
```

Run the database migrations:

```bash
php artisan migrate
```

---

## Running the Application

Run the application using:

```bash
composer run dev
```

On first run press y to install laravel/multiplex (this is a tabbed Terminal User Interface from Laravel)

App will be available at:

```text
http://localhost:8000
```

---

## Authentication

Unauthenticated users visiting:

```text
http://localhost:8000/
```

will be redirected to:

```text
http://localhost:8000/login
```

### Register

```text
http://localhost:8000/register
```

Create an account and you will be redirected to the task manager.

### Login

```text
http://localhost:8000/login
```

After logging in, you will be redirected to:

```text
http://localhost:8000/
```

---

## API Endpoints

### Authentication

| Method | Endpoint        | Description            | Auth |
| ------ | --------------- | ---------------------- | ---- |
| POST   | `/api/register` | Register a user        | No   |
| POST   | `/api/login`    | Login                  | No   |
| GET    | `/api/user`     | Get authenticated user | Yes  |
| POST   | `/api/logout`   | Logout                 | Yes  |

### Tasks

| Method    | Endpoint          | Description      | Auth |
| --------- | ----------------- | ---------------- | ---- |
| GET       | `/api/tasks`      | Get user's tasks | Yes  |
| POST      | `/api/tasks`      | Create a task    | Yes  |
| GET       | `/api/tasks/{id}` | Get a task       | Yes  |
| PUT/PATCH | `/api/tasks/{id}` | Update a task    | Yes  |
| DELETE    | `/api/tasks/{id}` | Delete a task    | Yes  |

Users can only access their own tasks.

---

## Project Structure

### Frontend

```text
resources/js/
├── app.js
├── components/
│   ├── Login.vue
│   ├── Register.vue
│   └── Main.vue
└── services/
    └── auth.js
```

### Backend

```text
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── AuthController.php
│           └── TaskController.php
└── Models/
    ├── Task.php
    └── User.php
```

### Routes

```text
routes/
├── api.php
└── web.php
```
