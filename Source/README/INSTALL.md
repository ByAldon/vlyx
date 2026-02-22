# Vlyx - Installation & Configuration Guide

This guide will walk you through the steps required to get your **Vlyx Bookmark Hub** up and running on your own server.

## 🛠 System Requirements
Before starting, ensure your server meets the following requirements:
* **PHP**: Version 8.1 or higher.
* **Web Server**: Apache (highly recommended for `.htaccess` support) or Nginx.
* **PHP Extensions**: `json` and `password_hash` support (standard in PHP 8+).

## 🚀 Step-by-Step Installation

### 1. Upload the Files
Upload the entire contents of the Vlyx package to your web server's public directory (e.g., `/public_html/` or `/var/www/html/`).

### 2. Prepare the Database Files
Vlyx uses a flat-file JSON database for speed and simplicity. 
1. Create a file named `users.json` in the root directory.
2. Create a file named `tokens.json` in the root directory.
3. Open both files and add a pair of curly braces `{}` to initialize them as empty JSON objects.

### 3. Folder Setup
Create a folder named `users` in the root directory. This folder will store the individual bookmark grids for every registered user.

### 4. File Permissions (Crucial)
For the system to save links and manage profiles, the web server must have **Write Permissions**. Use your FTP client or terminal (SSH) to set the following permissions:
* **`users/` folder**: CHMOD 777 (or 755 depending on server config)
* **`users.json`**: CHMOD 666 (or 644)
* **`tokens.json`**: CHMOD 666 (or 644)

### 5. Setup your Admin Account
1. Navigate to `yourdomain.com/admin.php`.
2. Use the "Create User" form to create your first account.
3. Make sure to select **Administrator** as the Role.
4. Click the action button to save the user.

### 6. Start using Vlyx
Navigate to your main URL and log in via the custom "Unlock Hub" portal.

## 🔒 Security Best Practices
* **JSON Protection**: The included `.htaccess` file is pre-configured to block direct browser access to your data files. Ensure your Apache server has `AllowOverride All` enabled to respect these rules.
* **SSL/HTTPS**: It is strongly recommended to use an SSL certificate. Since Vlyx handles login credentials, HTTPS ensures that your data is encrypted during transit.
* **Branding**: You can customize the accent color (currently Vlyx Blue) by changing the `--accent` variable in the CSS block of `index.php`.

---
*Vlyx Hub • Built by Aldon • 2026 • [GitHub](https://github.com/ByAldon/vlyx)*