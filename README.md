# 💻 PC Service Management System

A Laravel-based system for managing PC repair services with customer tracking and admin dashboard.

## 🚀 Key Features
* **Authentication:** Secure Admin Login using `AdminAuth` middleware.
* **Service Tracking:** Real-time status bar (New, On Going, Processing, Finished).
* **Admin Panel:** Full CRUD for Services, Statuses, and Service Types.
* **Customer Portal:** Report issues with location (Lat/Long) and track via phone number.

## 🛠 Tech Stack
* **Framework:** Laravel 11
* **Database:** MySQL
* **Frontend:** Bootstrap 5

## 🔧 Installation
1. `composer install`
2. `php artisan migrate --seed`
3. `php artisan serve`

## 📊 Status Flow
1. **New** - Initial report received.
2. **On Going** - Repair started.
3. **Processing** - Active repair work.
4. **Finished** - Ready for pickup.