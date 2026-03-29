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
- [Getting Started](#-getting-started)
- [API Endpoints](#api-endpoints)
- [Testing with Postman](#testing-with-postman)
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
- ✅ Guest booking — auto creates account + sends password by email
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
| Email | SMTP via Gmail App Password |

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
users              → id, name, email, phone, password, role, is_active
services           → id, title, description, duration, price, is_active, created_by
reservations       → id, user_id, service_id, date, start_time, end_time, status, notes
business_settings  → id, open_time, close_time, working_days
```

---

## 🚀 Getting Started

### 1 — Clone the repository

```bash
git clone https://github.com/FeRoRin/salon-reservation-api.git
cd salon-reservation-api
```

---

### 2 — Backend Setup (Laravel)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

---

### 3 — Configure the `.env` file

Open `salon-fresh/.env` and update these values:

```env
APP_NAME="Velvet Salon"
APP_URL=http://127.0.0.1:8000

# ── Database ──────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salon_db        # create this database in MySQL first
DB_USERNAME=root
DB_PASSWORD=                # your MySQL password (empty if none)

# ── Mail (Gmail SMTP) ─────────────────────────────
# IMPORTANT: You must use a Gmail App Password, NOT your normal Gmail password
# How to get an App Password:
#   1. Go to https://myaccount.google.com/security
#   2. Enable 2-Step Verification if not already on
#   3. Search "App passwords" → select "Mail" → Generate
#   4. Copy the 16-character password (shown with spaces like: xxxx xxxx xxxx xxxx)
#   5. Paste it below WITH quotes (spaces must be inside quotes)

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD="xxxx xxxx xxxx xxxx"    # ← App Password in quotes
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Velvet Salon"
```

> ⚠️ **Common mistake:** If your App Password has spaces and you don't wrap it in quotes, Laravel will throw `Failed to parse dotenv file`. Always use quotes: `MAIL_PASSWORD="xxxx xxxx xxxx xxxx"`

---

### 4 — Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed    # creates super admin + sample services
```

**Default super admin credentials (from seeder):**
```
Email:    superadmin@salon.com
Password: SuperAdmin@123
```

> ⚠️ Change the default password before deploying to production.

---

### 5 — Start the Laravel server

```bash
php artisan serve
# Running at: http://127.0.0.1:8000
```

---

### 6 — Frontend Setup (Vue 3)

The frontend lives in a **separate repository**.

```bash
# Clone the frontend repo separately
git clone https://github.com/FeRoRin/velvet-salon-frontend.git
cd velvet-salon-frontend
npm install
npm run dev
# Running at: http://localhost:5173
```

> Make sure the Laravel server is running on port 8000 before starting Vue.

---

## 🔗 API Endpoints

### Public Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Client registration |
| `POST` | `/api/login` | Login — all roles |
| `GET` | `/api/services` | List active services |
| `GET` | `/api/business-settings` | View business hours |
| `POST` | `/api/guest-booking` | Book + auto-create account |

### Client Routes (requires client token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/me` | Get my profile |
| `POST` | `/api/reservations` | Book an appointment |
| `GET` | `/api/my-reservations` | View my bookings |
| `DELETE` | `/api/reservation/{id}` | Cancel a booking |
| `PUT` | `/api/change-password` | Change own password |
| `POST` | `/api/logout` | Logout |

### Admin Routes (requires admin token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/admin/dashboard` | Dashboard statistics |
| `GET` | `/api/admin/reservations` | List all reservations |
| `POST` | `/api/admin/reservations` | Manual phone booking |
| `PUT` | `/api/admin/reservations/{id}` | Update reservation status |
| `GET` | `/api/admin/clients` | View all clients |

### Super Admin Routes (requires super_admin token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/services` | Create a service |
| `PUT` | `/api/services/{id}` | Update a service |
| `DELETE` | `/api/services/{id}` | Delete a service |
| `PUT` | `/api/business-settings` | Update business hours |
| `POST` | `/api/create-admin` | Create admin account |
| `GET` | `/api/super-admin/admins` | List all admins |
| `PATCH` | `/api/super-admin/admins/{id}/toggle-active` | Freeze / unfreeze admin |
| `PUT` | `/api/super-admin/admins/{id}/password` | Change admin password |
| `DELETE` | `/api/super-admin/admins/{id}` | Delete admin |
| `PUT` | `/api/super-admin/clients/{id}/password` | Change client password |

---

## 🧪 Testing with Postman

### Required headers on every request
```
Content-Type:  application/json
Accept:        application/json
```

### 1. Login
```json
POST /api/login
{
    "email": "superadmin@salon.com",
    "password": "SuperAdmin@123"
}
```
Copy the token from the response and add to all protected requests:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

### 2. Guest booking (no token needed)
```json
POST /api/guest-booking
{
    "name":             "Jane Doe",
    "email":            "jane@example.com",
    "phone":            "+1234567890",
    "service_id":       1,
    "reservation_date": "2026-03-20",
    "start_time":       "10:00",
    "notes":            "First visit"
}
```
Response includes the generated password — also sent to the provided email.

### 3. Book a reservation (authenticated client)
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

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          # Login, Register, Logout, ChangePassword
│   │   ├── Client/        # Services, Reservations, GuestBooking
│   │   ├── Admin/         # Dashboard, Reservations
│   │   └── SuperAdmin/    # Admin management, Clients
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/          # Form validation classes
├── Models/                # User, Service, Reservation, BusinessSetting
├── Services/              # ReservationService (business logic)
└── Traits/                # ApiResponse helper
database/
├── migrations/            # migration files
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
- Frozen accounts blocked at login

---

## 🐛 Common Issues

| Problem | Fix |
|---------|-----|
| `CORS policy blocked` | Make sure Vue runs on port 5173 and `config/cors.php` allows it |
| `Failed to parse dotenv file` | App Password has spaces — wrap in quotes in `.env` |
| `401 Unauthorized` | Token missing or expired — login again |
| `404 on /api/guest-booking` | Route not added to `routes/api.php` |
| `500 Server Error` | Check `storage/logs/laravel.log` for the real error |
| Email not sending | Check App Password is correct and Gmail 2FA is enabled |
| `ERR_CONNECTION_REFUSED` | Laravel server not running — run `php artisan serve` |

---

## 🔧 Useful Commands

```bash
# Clear all caches after .env or config changes
php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Re-run migrations from scratch (⚠️ deletes all data)
php artisan migrate:fresh --seed

# Check all registered routes
php artisan route:list

```

---

## 📄 License

MIT License — free to use and modify.
