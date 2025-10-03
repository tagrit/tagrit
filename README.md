# 🛠️ Tagrit ERP Installation Guide

## 📌 Prerequisites
Before installing Tagrit ERP, ensure your server meets the following requirements:

- **Web Server:** Apache/Nginx
- **PHP Version:** 8.2 or higher
- **Database:** MySQL 5.7+ or MariaDB 10+

## 🚀 Installation Steps

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/tagrit/tagrit.git
cd tagrit
```

### 2️⃣ Install PHP Dependencies
Make sure you have Composer installed, then run:
```bash
composer install 
```

### 3️⃣ Set Up Environment Configuration
Tagrit ERP uses a PHP-based .env.local.php file to manage database credentials and application configuration.

🔧 Steps:
1. Locate the env.local-sample.php file in the application/config
   folder.

2. Rename it to .env.local.php:

```bash
mv application/config/env.local-sample.php .env.local.php
```
3. Open the .env.local.php file and update the values accordingly:

```bash
<?php
putenv('APP_BASE_URL=http://localhost/tagrit'); // or your production URL
putenv('APP_DB_USERNAME=root');                 // your DB username
putenv('APP_DB_PASSWORD=your-password');        // your DB password
putenv('APP_DB_NAME=tagrit');                   // your DB name
putenv('APP_DB_HOSTNAME=localhost');            // usually localhost
putenv('APP_ENC_KEY=85bec75a1a6136881a01c08b1fdc31d8'); // encryption key
```

### 4️⃣  Set Up the Database
1. Log in to MySQL or MariaDB and create the database:

```bash
CREATE DATABASE tagrit;
```

2. Import the database schema provided in the install.sql file:

```bash
mysql -u your_username -p tagrit < install.sql
```

### 5️⃣ Access Tagrit ERP

Open your browser and go to:

👉 [http://localhost/tagrit](http://localhost/tagrit)



## 🔑 Default Admin Credentials

Use the following credentials to log in as an administrator:

| **Field**   | **Value**         |
|------------|-------------------|
| **Email**  | `admin@admin.com` |
| **Password** | `password`        |


## 📜 Conventions

| **Prefix**   | **Description**              | **Example** |
|------------|----------------------|----------------|
| `ft-` | New Feature | `ft-create-event` |
| `bf-` | Bug Fixes      | `bf-event-listing` |
| `hf-` | Hot Fixes      | `hf-currency-dropdown` |
| `ch-` | Chores      | `ch-order-cleanup` |


## 🙌 Contribution
Feel free to fork the repository and submit pull requests!

Happy coding! 🚀





