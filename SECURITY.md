# Security Policy ⚡

We take the security of Vlyx Hub seriously. Because this project is "Source Available," transparency is key to maintaining a safe environment for all users.

## Supported Versions

Currently, only the latest stable version of Vlyx Hub receives security updates.

| Version | Supported          |
| ------- | ------------------ |
| v1.1.7  | :white_check_mark: |
| < 1.1.7 | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability within Vlyx Hub, please **do not** open a public issue. Instead, follow these steps:

1. **Private Report**: Send a detailed description of the vulnerability to the maintainer via GitHub Private Reporting (if enabled) or via email.
2. **Details**: Include steps to reproduce the issue, the potential impact, and any suggested fixes.
3. **Response**: You will receive an acknowledgment within 48–72 hours.
4. **Resolution**: Once a fix is developed, a new official release (e.g., v1.1.8) will be published.

## Our Security Standards

To keep Vlyx Hub as secure as possible, we implement the following by default:
* **Password Hashing**: All user passwords are encrypted using `password_hash()`.
* **Data Protection**: We provide `.htaccess` rules to prevent unauthorized browser access to your JSON database files.
* **Minimal Surface**: By keeping the code lightweight and free of external dependencies, we reduce the risk of third-party vulnerabilities.

## Personal Responsibility

Since Vlyx Hub is a self-hosted tool, users are responsible for securing their own server environment. We strongly recommend:
* Using **HTTPS** for all dashboard access.
* Setting correct file permissions on the `users/` directory and `users.json`.
* Regularly checking for official updates via the built-in update checker.

---
*Vlyx Hub • v1.1.7 • Security Policy*