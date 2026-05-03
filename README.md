# IPB-1.3-PHP8-Modernized

A modernized distribution of **Invision Power Board 1.3**, fully patched for **PHP 8.x** and **MariaDB** compatibility. This project preserves the classic, lightweight "pre-bloat" UI of the early 2000s while ensuring it runs securely and efficiently on modern server stacks.

## 🚀 Key Features

*   **PHP 8.x Ready**: Comprehensive patches for legacy code to eliminate deprecated function errors and syntax issues.
*   **MySQLi Implementation**: Replaced the legacy `mysql` driver with `mysqli` for modern database connectivity.
*   **MariaDB Support**: Optimized for MariaDB and MySQL 8+ environments using `utf8mb4` encoding by default.
*   **Slim Installer**: A streamlined `sm_install.php` that handles environment checks, `mysqli` configuration, and admin setup without the bloat.
*   **Performance Focused**: Maintained the original lightweight footprint with native CSS and minimal JavaScript dependencies.

## 📦 Installation

1.  **Database**: Create a new database and import the provided SQL dump via phpMyAdmin or Adminer.
2.  **Upload**: Upload the repository files to your web directory.
3.  **Configure**: Run `sm_install.php` in your browser.
4.  **Security**: Once the `conf_global.php` is generated, **delete** `sm_install.php` from your server immediately.

---

## 🎨 UI/UX Philosophy

This project stays true to the "classic" aesthetic. We prioritize the original Invision Power Board 1.3 layout over "modern" social-media-style interfaces, ensuring the board remains fast, familiar, and highly customizable.

---

**Developed & Maintained for the Modern Web.**
