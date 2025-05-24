# Content Scheduler

A simple content scheduling platform built with Laravel 12, Blade, Laravel Sanctum for authentication, and Livewire for dynamic interactivity.

## 🚀 Features

- User registration and login (using Laravel Sanctum)
- Secure authentication
- Blade-based UI
- Livewire components for reactive interfaces
- Easily extendable for scheduling content, managing users, and handling permissions

## 🧰 Tech Stack

- PHP (Laravel 12)
- Blade templating engine
- Laravel Sanctum (API token authentication)
- MySQL / SQLite (default DB support)
- GitHub for version control

## 🔧 Installation with serve

- Clone the repository:

```bash
git clone git@github.com:omarabuelkhier/content-scheduler.git
cd content-scheduler
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
