# 🍽️ Dineflo v2 - Advanced Restaurant POS & Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3-fca311?style=for-the-badge&logo=filament)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/Status-Production--Ready-success?style=for-the-badge)](https://3flo.net)
[![Dineflo CI/CD - Tests](https://github.com/3flonet/dineflo/actions/workflows/tests.yml/badge.svg)](https://github.com/3flonet/dineflo/actions/workflows/tests.yml)

![Dineflo Features Preview](features.png)

**Dineflo** is a comprehensive, multi-tenant restaurant management system designed for scalability and ease of use. From digital menus to real-time kitchen management, Dineflo covers every aspect of modern restaurant operations.

---

## ✨ Key Features

For a comprehensive deep dive into the **44+ advanced features** and detailed system architecture, please refer to our [**Dineflo Project Overview & Feature Guide**](PROJECT_OVERVIEW.md).


### 🏢 Multi-Tenancy Architecture
- **Isolated Tenants:** Each restaurant operates in its own secure environment.
- **Role-Based Access (RBAC):** Granular permissions for Super Admins, Restaurant Owners, and Staff.
- **Tenant Auto-Scoping:** Secure data isolation using Global Eloquent Scopes.

### 💳 POS & Ordering System
- **Digital Menu:** Beautiful, responsive customer-facing menu with QR code integration.
- **Waiter Call System:** Real-time table-to-staff notifications via WebSockets.
- **Advanced Cart:** Support for variants, add-ons, and upsells.
- **EDC Payment Integration:** Native support for physical bank terminals with MDR fee tracking and reconciliation.
- **Flexible Payments:** Integrated with Midtrans for seamless online transactions.
- **Draggable & Customizable Dashboard:** Personalize widget layouts per-user with an interactive drag-and-drop interface.


### 🍳 Operational Management
- **Kitchen Display System (KDS):** Real-time order monitoring for kitchen efficiency.
- **Inventory Tracking:** Manage ingredients and stock movements automatically.
- **Loyalty Program:** Tiered loyalty points (Silver, Gold) to increase customer retention.
- **Manual Menu Reordering:** Easily organize menu categories and items using a simple drag-and-drop handles.
- **Marketing Tools:** Integrated Email and WhatsApp campaign management.


### 🔐 Robust License Protection
- **Remote Verification:** Centralized license management via 3Flo LicenseHub.
- **Security:** HMAC-SHA256 digital signatures for all server responses.
- **Grace Period:** 7-day operational safety net if a license expires.
- **Auto-Sync:** Background heartbeat pings keep the system status updated.

---

## 🛠️ Tech Stack

- **Framework:** [Laravel 11](https://laravel.com)
- **Admin Panel:** [Filament v3](https://filamentphp.com)
- **Frontend Logic:** [Livewire](https://livewire.laravel.com) & [Alpine.js](https://alpinejs.dev)
- **Database:** MySQL 8.0+
- **Real-time:** [Laravel Reverb](https://reverb.laravel.com) (WebSocket)
- **Styling:** TailwindCSS
- **Reporting:** DomPDF & Maatwebsite Excel

---

## 🚀 Quick Start & Installation

Dineflo is designed for easy setup on both local and production environments. For step-by-step instructions tailored to your environment, please refer to the dedicated guides below:

### 🏠 [Localhost Installation (Development)](LOCALHOST_INSTALLATION.md)
Detailed guide for setting up Dineflo on a local machine using **Laragon**, **XAMPP**, or other local servers including:
*   Project cloning and dependency installation (`composer`, `npm`).
*   Development asset compilation (`npm run dev`).
*   Local activation and web-based installer steps.
*   **Automatic Restaurant Trial:** New restaurants automatically receive a complimentary trial package upon registration.


### 🌐 [Production Deployment Guide](DEPLOYMENT_INSTALLATION.md)
Essential steps for deploying Dineflo to a production server, including:
*   Server requirements (PHP 8.2+, MySQL 8, SSL).
*   Correct file permissions and production asset building (`npm run build`).
*   Configuring cron jobs for the scheduler and Supervisord for **Reverb/WebSockets**.
*   Production-ready security practices.

### 🧙‍♂️ Smart Installation Wizard
Dineflo now features an intelligent web-based installer that simplifies deployment:
*   **Auto-Environment:** Automatically detects and sets `APP_TIMEZONE` (Default: UTC).
*   **Real-time Auto-config:** Dynamically generates **Laravel Reverb** (WebSocket) settings based on your `APP_URL`.
*   **Auto-VAPID Generation:** Automatically generates **Web Push Notification** keys (`VAPID_PUBLIC_KEY` / `PRIVATE_KEY`) for out-of-the-box PWA notifications.
*   **Self-Starting Engine:** Automatically attempts to start the **Reverb Server** in the background upon completion (on VPS/Linux environments using `nohup`).

---

---

## 📅 Maintenance & Commands

| Command | Description |
| :--- | :--- |
| `php artisan license:ping` | Manually sync license status with server. |
| `php artisan license:send-warnings` | Send expiration emails to customers (runs daily). |
| `php artisan dineflo:sync-permissions` | Sync & update permissions to all restaurant roles. |
| `php artisan schedule:work` | Start the local scheduler for background tasks. |
| `php artisan optimize:clear` | Clear all system caches. |


---

## 🏗️ Folder Structure (Key Locations)

- `app/Models/`: Core business logic and database entities.
- `app/Filament/`: Admin, HQ, and Restaurant dashboard configurations.
- `app/Livewire/`: Interactive frontend components (Menu, Cart).
- `app/Services/`: Third-party integrations (LicenseHub, Midtrans).
- `resources/views/`: Blade templates and email designs.

---

## 📞 Support & Branding
Created with ❤️ by **3Flo Team**.
For technical support, contact us at [support@3flo.net](mailto:support@3flo.net) or visit [3flo.net](https://3flo.net).

---
© 2026 3Flo.net. All rights reserved.
