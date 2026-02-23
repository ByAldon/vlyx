# ⚡ Vlyx Hub Installation & Update Guide

Follow this guide to set up Vlyx Hub for the first time or to safely update your existing installation.

---

## ⚠️ CRITICAL: Updating an existing installation

If you are updating Vlyx Hub to a newer version, please read this carefully:

> [!CAUTION]
> **DO NOT delete or overwrite the following files/folders during an update:**
> * `users/` (Folder containing individual user bookmark grids)
> * `users.json` (The core database containing account credentials)
> * `tokens.json` (Used for password recovery sessions)
>
> These files act as your **database**. Deleting them will **PERMANENTLY RESET** the entire system, and all user data will be lost.

To update, only replace the `.php` files and the `README.md`/`INSTALL.md` files.

---

## 🚀 Fresh Installation

Vlyx Hub is designed to be "Self-Healing." You do not need to create database files manually.

### 1. Requirements
* **PHP**: Version 8.1 or higher
* **Web Server**: Apache (recommended for `.htaccess` support) or Nginx
* **Permissions**: The root directory must be writable by the web server.

### 2. Upload
Upload all source files to your web server's public directory.

### 3. Automatic Setup
1.  Navigate to your site URL in a web browser.
2.  The **Auto-Installer** will detect a fresh environment and create the necessary `users/` folder and `users.json` database automatically.
3.  The **Setup Wizard** will appear to guide you through the initial configuration.

### 4. Create Admin Account
1.  Read the welcome message and click **Next**.
2.  Enter a username, an optional email (highly recommended for recovery), and a strong password.
3.  The first account created is automatically granted **Administrator** privileges.

### 5. Login
After setup, you will be redirected to the **Unlock Portal**. Log in with your new credentials to start adding links.

---

## 🔒 Security Post-Install
* **`.htaccess`**: Vlyx includes a `.htaccess` file that prevents direct browser access to your JSON database files. Ensure your server respects this file.
* **HTTPS**: It is strongly recommended to run Vlyx over a secure HTTPS connection to protect your login data.

---
*Vlyx Hub • v1.1.5 • Built by Aldon • 2026*