# ⚡ Vlyx Hub

Vlyx Hub is a lightweight, self-hosted bookmark management system designed for speed, simplicity, and privacy. Built with PHP and powered by a JSON flat-file database, it provides a centralized dashboard for all your essential links without the need for a complex SQL setup.

**Current Version: v1.1.7**

---

## 🌟 Features

* **Lightning Fast Interface**: A clean, modern dark-mode grid for all your bookmarks.
* **Integrated Search Engine**: Search the web directly from your hub. Supports DuckDuckGo, Google, Bing, Startpage, Brave Search, and Qwant.
* **Personalized Navigation**: Choose whether bookmarks and search results open in the same window or a new tab independently.
* **User Management**: Support for multiple users with specific roles (Admin/User).
* **Auto-Installer**: "Self-healing" logic that automatically sets up the environment and database upon first launch.
* **Secure by Design**: Password hashing (bcrypt) and protected JSON data files via `.htaccess`.
* **Update Checker**: Built-in system to notify you of new official releases from GitHub.

---

## 🚀 Quick Start

### Prerequisites
* PHP 8.1 or higher.
* Web server (Apache recommended for `.htaccess` support).

### Installation
1.  Upload the contents of the `Source/` directory to your web server.
2.  Ensure the root directory is writable.
3.  Visit your URL and follow the **Setup Wizard** to create your primary administrator account.

For detailed instructions, see **[INSTALL.md](INSTALL.md)**.

---

## 🔒 Security

We prioritize your data security. Access to database files is restricted, and only the latest version receives active security patches.

For vulnerability reporting and standards, please refer to **[SECURITY.md](SECURITY.md)**.

---

## 🛠️ Contributing

Contributions are welcome! Whether it's a bug fix or a new feature, feel free to fork the repo and submit a PR.

See **[CONTRIBUTING.md](CONTRIBUTING.md)** for more details.

---

## 📜 License

Distributed under the MIT License. [cite_start]See `LICENSE` for more information[cite: 3].

*Vlyx Hub • Built by Aldon • 2026*
