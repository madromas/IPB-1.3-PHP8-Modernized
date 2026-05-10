# IPB-1.3-PHP8-Modernized

A modernized distribution of **Invision Power Board 1.3**, fully patched for **PHP 8.4** and **MariaDB** compatibility. This project preserves the classic, lightweight "pre-bloat" UI of the early 2000s while ensuring it runs securely and efficiently on modern server stacks.

<img width="700" alt="69fe1ed9e6e5452a9d127996" src="https://github.com/user-attachments/assets/23cbc0ee-2d1c-4d82-b66e-ff92e1669cb4" />

<img width="700" alt="69fe1935debc337d903b352c" src="https://github.com/user-attachments/assets/80136a29-e673-46e7-8a9b-411daa97764b" />

<img width="700" alt="Screenshot 2026-05-08 125949" src="https://github.com/user-attachments/assets/7174f5bd-dbd3-414f-ab02-fadb7eaa9734" />

<img width="700" alt="Screenshot 2026-05-10 105645" src="https://github.com/user-attachments/assets/d9784a70-4a0a-4d60-af50-22f3cb5b09d4" />

<img width="700" alt="Screenshot 2026-05-03 113528" src="https://github.com/user-attachments/assets/98796223-3904-4c2c-bdec-3966799bfe62" />

## 🚀 Key Features

*   **PHP 8.x Ready**: Comprehensive patches for legacy code to eliminate deprecated function errors and syntax issues.
*   **MySQLi Implementation**: Replaced the legacy `mysql` driver with `mysqli` for modern database connectivity.
*   **MariaDB Support**: Optimized for MariaDB and MySQL 8+ environments using `utf8mb4` encoding by default.
*   **Slim Installer**: A streamlined `sm_install.php` that handles environment checks, `mysqli` configuration, and admin setup.
*   **Performance Focused**: Maintained the original lightweight footprint with native CSS and minimal JavaScript dependencies.

## ✨ Modern Enhancements

*   **IBF Portal 4.0**: Fully integrated portal system to transform your forum into a community hub.
*   **TinyMCE Integration**: Replaced the legacy posting interface with a modernized TinyMCE editor for a better rich-text experience.
*   **HTML Purifier**: Integrated a modernized standalone security engine to protect against XSS and fix broken HTML tags.
*   **Security Patches**: Fixed legacy issues with search result insertions and anti-spam image generation.
*   **OP (Original Poster) Identification**: Implemented a specialized "Author" badge in the topic view (postbit). 
*   **Real-time User Presence**: Integrated a dynamic Online/Offline status indicator for every user profile in the forum view.
*   **Lofi Version 2.0**: Completely overhauled the legacy text-only lofiversion with quick reply.
*   **Forum Icon Customization**: Developed a modular system to override standard folder icons with custom GIFs assigned via the Admin CP.
*   **Reputation System**: Added community rating engine, that allows users to vote on each other's profiles and leave comments explaining their feedback.
*   **Advanced Topic Ratings**: Replaced the legacy star-based system with a streamlined, AJAX-ready voting engine integrated directly into the first post for increased user engagement.
*   **Warn Users by %**: Increase or decrease user warning lavel.
*   **PHP -> HTML Converter**: Converter of php links to html links. For search robots, catalogs, and better indexing of your forum.
*   **Awards System**: Allows you to add, delete, and edit awards for users from the admin panel.
*   **Favorites System**: Enhanced user bookmarking. Manage your favorite topics.

## 📦 Installation

1.  **Upload**: Upload the repository files to your web directory.
2.  **Configure**: Run `sm_install.php` in your browser.
3.  **Security**: Once the `conf_global.php` is generated, **delete** `sm_install.php` and `database.sql` from your server immediately.

---

## 🎨 UI/UX Philosophy

This project stays true to the "classic" aesthetic. We prioritize the original Invision Power Board 1.3 layout over "modern" social-media-style interfaces, ensuring the board remains fast, familiar, and highly customizable.

---

**Developed & Maintained for the Modern Web.**
