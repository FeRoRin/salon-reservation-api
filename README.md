# 💈 Salon Reservation System API

A production-ready RESTful API for a salon booking system built with **Laravel 12**, **MySQL**, and **Laravel Sanctum**.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Roles](#roles)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Testing with Postman](#testing-with-postman)
- [API Documentation](#api-documentation)
- [Default Credentials](#default-credentials)

---

## 🎯 Overview

This is an API-only backend for a salon booking system. It supports three roles (Super Admin, Admin, Client), handles reservation scheduling with business logic validation, and provides a dashboard with statistics.

---

## ✨ Features

- ✅ Role-based authentication (Super Admin / Admin / Client)
- ✅ Token-based auth with Laravel Sanctum
- ✅ Reservation booking with overlap prevention
- ✅ Business hours & working days validation
- ✅ Auto end-time calculation from service duration
- ✅ Admin dashboard with statistics
- ✅ Search, filter, and sort reservations
- ✅ Manual phone bookings by admin
- ✅ Rate limiting on login and register
- ✅ Clean JSON responses on all endpoints
- ✅ Form Request validation on every input

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| Database | MySQL 8+ |
| Authentication | Laravel Sanctum |
| PHP Version | 8.2+ |
| Password Hashing | bcrypt |
| Validation | FormRequest classes |

---

## 👥 Roles

| Role | Permissions |
|------|------------|
| `super_admin` | Full access — manage admins, services, business settings |
| `admin` | Manage reservations, view dashboard, phone bookings |
| `client` | Book services, view/cancel own reservations |

---

## 🗄 Database Schema
```
users              → id, name, email, phone, password, role
services           → id, title, description, duration, price, is_active, created_by
reservations       → id, user_id, service_id, date, start_time, end_time, status, notes
business_settings  → id, open_time, close_time, working_days
```

---

## ⚙️ Installation

### Requirements
- PHP 8.2+
- Composer
- MySQL 8+

### Steps
```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/salon-reservation-api.git
cd salon-reservation-api

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
DB_DATABASE=salon_db
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. Seed the database
php artisan db:seed

# 8. Start the server
php artisan serve
```

---

## 🔗 API Endpoints

### Public Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Client registration |
| `POST` | `/api/login` | Login — all roles |
| `GET` | `/api/services` | List active services |
| `GET` | `/api/business-settings` | View business hours |

### Client Routes (requires client token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/me` | Get my profile |
| `POST` | `/api/reservations` | Book an appointment |
| `GET` | `/api/my-reservations` | View my bookings |
| `DELETE` | `/api/reservation/{id}` | Cancel a booking |
| `POST` | `/api/logout` | Logout |

### Admin Routes (requires admin token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/admin/dashboard` | Dashboard statistics |
| `GET` | `/api/admin/reservations` | List all reservations |
| `POST` | `/api/admin/reservations` | Manual phone booking |
| `PUT` | `/api/admin/reservations/{id}` | Update reservation status |

### Super Admin Routes (requires super_admin token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/services` | Create a service |
| `PUT` | `/api/services/{id}` | Update a service |
| `DELETE` | `/api/services/{id}` | Delete a service |
| `PUT` | `/api/business-settings` | Update business hours |
| `POST` | `/api/create-admin` | Create admin user |

---

## 🧪 Testing with Postman

### 1. Login
```json
POST /api/login
{
    "email": "superadmin@salon.com",
    "password": "SuperAdmin@123"
}
```
Copy the token from the response.

### 2. Use the token
In every protected request, add this header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

### 3. Create a service
```json
POST /api/services
{
    "title": "Classic Haircut",
    "description": "Precision cut and blow dry",
    "duration": 45,
    "price": 35.00,
    "is_active": true
}
```

### 4. Book a reservation
```json
POST /api/reservations
{
    "service_id": 1,
    "reservation_date": "2026-03-10",
    "start_time": "10:00",
    "notes": "First visit"
}
```

### Reservation Business Rules
1. Date must be a working day
2. Time must be within business hours
3. No overlapping bookings allowed
4. End time is auto-calculated from service duration

---

## 📄 API Documentation

Full API documentation is available here:

[View Documentation](docs/salon-api-documentation.pdf)

---

## 🔐 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@salon.com | SuperAdmin@123 |

> ⚠️ Change the default password before deploying to production.

---

## 📁 Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          # Login, Register, Logout
│   │   ├── Client/        # Services, Reservations
│   │   ├── Admin/         # Dashboard, Reservations
│   │   └── SuperAdmin/    # Services, Settings, Admins
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/          # Form validation classes
├── Models/                # User, Service, Reservation, BusinessSetting
├── Services/              # ReservationService (business logic)
└── Traits/                # ApiResponse helper
database/
├── migrations/            # 5 migration files
└── seeders/               # Super Admin + default settings
routes/
└── api.php                # All API routes
```

---

## 🛡 Security

- Passwords hashed with bcrypt
- All routes protected with Sanctum tokens
- Role enforcement via RoleMiddleware
- Rate limiting on login (5/min) and register (10/min)
- All inputs validated via FormRequest classes
- Mass assignment prevented via `$fillable`

---

## 📄 License

MIT License — free to use and modify.
