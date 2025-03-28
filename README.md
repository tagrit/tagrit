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

### 2️⃣ Set Up the Database
1. Log in to MySQL and create a new database:
```bash
CREATE DATABASE tagrit;
```
2. Import the provided SQL file:
```bash
mysql -u your_username -p tagrit < install.sql
```
### 3️⃣ Configure your database credentials inside the app-config.php File inside application/config folder

```php
// Define database credentials e.g.
$db_config = [
    'local' => [
        'BASE_URL'  => 'http://localhost/tagrit',
        'USERNAME'  => 'root',
        'PASSWORD'  => '',
        'DB_NAME'   => 'tagrit'
    ],
];
```

### 4️⃣ Access Tagrit ERP

Open your browser and visit:

```
http://localhost/tagrit
```

## 🔑 Default Admin Credentials

Use the following credentials to log in as an administrator:

| **Field**   | **Value**              |
|------------|----------------------|
| **Email**  | `admin@admin.com`  |
| **Password** | `Crazy534`         |


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





