# Petrex Estate PHP Website

**Petrex Estate and Property Managers** — A complete real estate website built with pure PHP, HTML, Tailwind CSS, and MySQL. Designed for Nigerian real estate companies and optimised for Namecheap shared hosting with cPanel.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5 + Tailwind CSS (CDN) + Vanilla JS |
| Backend | PHP 8+ (pure PHP, no framework) |
| Database | MySQL |
| Auth | PHP Sessions |
| Font | Google Fonts — Poppins |

---

## Features

- 🏠 Property listings with filters (type, state, bedrooms, price range)
- 👤 Agent profiles with ratings
- 📝 Blog with publish/draft toggle
- 📩 Inquiry system (contact & property-specific)
- 🔐 User registration & login
- 🛡️ Admin dashboard with full CRUD
- 📱 Fully mobile responsive
- 💰 Prices in Nigerian Naira (₦)
- 🔒 Prepared statements throughout (SQL injection protection)

---

## File Structure

```
petrex-estate-php/
├── config/
│   ├── database.php        # DB connection settings
│   └── auth.php            # Session helpers
├── includes/
│   ├── header.php          # Navbar & HTML head
│   └── footer.php          # Footer & closing HTML
├── database/
│   └── schema.sql          # MySQL schema + seed data
├── properties/
│   ├── index.php           # Property listings with filters
│   └── single.php          # Single property detail
├── agents/
│   └── index.php           # All agents
├── blog/
│   ├── index.php           # Blog listing
│   └── single.php          # Single blog post
├── contact/
│   └── index.php           # Contact form
├── auth/
│   ├── login.php           # Login page
│   ├── register.php        # Registration page
│   └── logout.php          # Logout handler
├── actions/
│   └── submit-inquiry.php  # Inquiry form handler
├── admin/
│   ├── index.php           # Dashboard
│   ├── includes/
│   │   └── sidebar.php     # Admin sidebar
│   ├── properties/
│   │   ├── index.php       # List properties
│   │   ├── add.php         # Add property
│   │   └── edit.php        # Edit property
│   ├── agents/
│   │   ├── index.php       # List agents
│   │   ├── add.php         # Add agent
│   │   └── edit.php        # Edit agent
│   ├── blog/
│   │   ├── index.php       # List blog posts
│   │   ├── add.php         # Add blog post
│   │   └── edit.php        # Edit blog post
│   └── inquiries/
│       └── index.php       # Manage inquiries
├── index.php               # Homepage
├── .htaccess               # Apache config
└── README.md               # This file
```

---

## Namecheap cPanel Setup Guide

### Step 1 — Create MySQL Database

1. Log in to **Namecheap cPanel** → **MySQL Databases**
2. Under **Create New Database**, type a name (e.g. `petrex_db`) → click **Create Database**
3. Under **Create New User**, create a user with a strong password
4. Under **Add User to Database**, add your user to the database and grant **ALL PRIVILEGES**
5. Note down:
   - Database name (e.g. `cpanelusername_petrex_db`)
   - Database user (e.g. `cpanelusername_petrexuser`)
   - Password you just created

### Step 2 — Import the Database Schema

1. In cPanel, open **phpMyAdmin**
2. Click your new database in the left panel
3. Click the **Import** tab at the top
4. Click **Choose File** and select `database/schema.sql`
5. Click **Go** to import

### Step 3 — Configure Database Credentials

Open `config/database.php` and update:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'cpanelusername_petrexuser');  // Your DB username
define('DB_PASS', 'your_strong_password');        // Your DB password
define('DB_NAME', 'cpanelusername_petrex_db');    // Your DB name
define('SITE_URL', 'https://petrex-estate.com');  // Your domain
```

### Step 4 — Upload Files to Namecheap

1. In cPanel → **File Manager** → navigate to `public_html`
2. Click **Upload** and upload all files, maintaining the folder structure
3. **OR** use an FTP client (FileZilla) with your FTP credentials from cPanel

> ⚠️ Make sure the entire folder structure is preserved when uploading.

### Step 5 — Test Your Website

Visit `https://yourdomain.com` to see your homepage.

---

## Default Admin Login

| Field | Value |
|-------|-------|
| URL | `https://yourdomain.com/auth/login.php` |
| Email | `admin@petrex-estate.com` |
| Password | `password` |

> ⚠️ **IMPORTANT:** Change the admin password immediately after first login via the database or by updating the hash in the `users` table.

To generate a new password hash:
```php
<?php echo password_hash('your_new_password', PASSWORD_DEFAULT); ?>
```
Run this snippet, then update the `password` column in the `users` table via phpMyAdmin.

---

## Troubleshooting

### "Connection failed" error
- Double-check `config/database.php` credentials
- Ensure you added the DB user to the database with ALL PRIVILEGES in cPanel

### Blank white page
- Enable PHP error display temporarily: add `ini_set('display_errors', 1);` at the top of `index.php`
- Check cPanel → **Error Logs** for PHP errors

### 404 errors on pages
- Ensure `.htaccess` is uploaded
- In cPanel → **Apache Handlers** or contact Namecheap support to enable `mod_rewrite`

### Images not showing
- Add image URLs when creating/editing properties in the admin panel
- Use publicly accessible image URLs (e.g. from Cloudinary, Unsplash, or your own uploads)

### Session issues / not staying logged in
- Ensure PHP sessions are enabled on your hosting plan
- Check that your PHP version is 8.0+ in cPanel → **Select PHP Version**

---

## Security Notes

- All database queries use **prepared statements** (protection against SQL injection)
- All output is escaped with `htmlspecialchars()` (XSS protection)
- Passwords use `password_hash()` / `password_verify()` with bcrypt
- Directory listing is disabled via `.htaccess`
- Config files are protected from direct access

---

## Design

- Primary color: `#1a3c6e` (dark blue)
- Accent color: `#f59e0b` (amber/gold)
- Font: Poppins (Google Fonts)
- Framework: Tailwind CSS (CDN)

---

&copy; 2024 Petrex Estate and Property Managers | Victoria Island, Lagos, Nigeria
