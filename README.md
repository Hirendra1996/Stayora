# Farmlelo - Local Development Guide

Farmlelo is a custom PHP MVC web application for booking and listing farmhouses.

---

## 🚀 Quick Start (Local Setup)

### 1. Prerequisites
- **XAMPP** (or Apache + MySQL + PHP 8.1+)
- **Composer** (optional, recommended)
- Required PHP Extensions: `mysqli`, `pdo_mysql`, `curl`, `json`, `mbstring`, `fileinfo`, `openssl`

---

### 2. Fast Setup (1 Command)

Open your terminal in the project directory (`c:\xampp\htdocs\Farmlelo`) and run:

```bash
# Option A: With Composer
composer setup

# Option B: Directly with PHP
php setup.php

# Option C: On Windows
Double-click setup.bat
```

The setup script automatically:
1. Validates your PHP version and extensions.
2. Creates `.env` from `.env.example` if not present.
3. Ensures all required `assets/images/uploads/` folders exist.
4. Connects to MySQL, creates the `farmlelo` database, and imports `database/farmlelo.sql` if tables are missing.

---

### 3. Starting the Local Development Server

Choose either method below:

#### Method A: Built-in PHP Server (Recommended / Fast)
```bash
composer dev
# Or
php -S localhost:8000 router.php
# Or double-click serve.bat
```
👉 Open **[http://localhost:8000](http://localhost:8000)** in your browser.

#### Method B: XAMPP Apache
1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Place the project folder in `C:\xampp\htdocs\Farmlelo`.
3. Open **[http://localhost/Farmlelo/](http://localhost/Farmlelo/)** in your browser.

---

## ⚙️ Environment Configuration (`.env`)

Configuration settings are stored in `.env`:

```env
# Database Settings
DB_HOST=localhost
DB_PORT=3306
DB_NAME=farmlelo
DB_USER=root
DB_PASS=

# Application Settings
APP_NAME="Farm Lelo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

---

## 🗄️ Database Import & Backups

- The initial database dump is located at: `database/farmlelo.sql`
- To import manually using phpMyAdmin:
  1. Open [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
  2. Create a new database named `farmlelo` (utf8mb4_unicode_ci)
  3. Click **Import** and select `database/farmlelo.sql`
- Or via MySQL CLI:
  ```bash
  mysql -u root -p farmlelo < database/farmlelo.sql
  ```

---

## 🔑 Default Test Accounts

| Role | Email | Notes |
|---|---|---|
| **Admin** | `admin.farmlelo@gmail.com` | Access at `/admin/login` |
| **Admin** | `admin@gmail.com` | Access at `/admin/login` |
| **Owner** | `owner@farmlelo.com` | Access at `/owner/login` |
| **User** | `user@farmlelo.com` | Access at `/login` |

---

## 📁 Project Structure

```
Farmlelo/
├── app/
│   ├── Config/          # Database connection and configs
│   ├── Controllers/     # MVC Controllers (Admin, Owner, User, Home)
│   ├── Helpers/         # Helper functions (base_url, asset, url, redirect)
│   ├── Middleware/      # Auth and access control middleware
│   ├── Models/          # Database models
│   ├── Views/           # View templates and UI layouts
│   └── routes.php       # Application router
├── assets/
│   ├── css/             # Stylesheets (style.css)
│   ├── images/          # Images, logos, and uploads
│   └── js/              # Client-side JavaScript
├── database/
│   └── farmlelo.sql     # Database export & schema
├── .env.example         # Environment template
├── composer.json        # Dependencies & autoloading
├── index.php            # Main web entry point
├── router.php           # Local dev server router
├── setup.php            # Automated setup script
├── serve.bat            # 1-click Windows server launcher
└── setup.bat            # 1-click Windows setup launcher
```
aksdf





admin@gmail.com
Admin@123