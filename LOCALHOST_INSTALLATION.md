# 🏠 Localhost Installation Guide (Development)

This guide documents the steps to set up **Dineflo** for local development using environments like **Laragon** (recommended) or **XAMPP/MAMP**.

---

## 🛠️ Prerequisites

Ensure your local machine has the following:
- **PHP 8.2+** with necessary extensions (intl, gd, bcmath, pdo_mysql).
- **Composer** (v2+).
- **Node.js & NPM** (v18+).
- **MySQL 8.0+** or MariaDB.
- **Local Server:** [Laragon](https://laragon.org) is highly recommended for Windows because it manages virtual hosts and automatic database creation.

---

## 🚀 Installation Steps

### 1. Clone the Project
Open your terminal and navigate to your local server's root directory (e.g., `C:\laragon\www`):
```bash
git clone https://github.com/3flonet/dineflo.git
cd dineflo
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Setup Frontend Assets
Install Node dependencies and start the development server for real-time asset updates (Hot Module Replacement):
```bash
npm install
# Keep this running in a separate terminal during development
npm run dev
```

### 4. Basic Environment Setup
Create your local environment file:
```bash
cp .env.example .env
```
> [!TIP]
> You don't need to manually configure database or app settings in `.env` yet. The **Web Installer** will handle this for you!

### 5. Setup Local Virtual Host (Laragon Only)
- Laragon will automatically detect the new folder and suggest creating `http://dineflo.test`.
- Make sure to **Start All Services** in Laragon.
- If you use XAMPP, you'll need to manually configure [Apache Virtual Hosts](https://httpd.apache.org/docs/2.4/vhosts/examples.html) pointing to the `public/` directory.

---

## 🔑 Activation & Smart Web Installer

1. **Access the Application:** Open `http://dineflo.test` in your browser. You will be automatically redirected to the **Installation Wizard**.
2. **Step 1: Requirements Check:** The system verifies folder permissions and PHP extensions.
3. **Step 2: App Configurations & Real-time (New!)**:
   - **App Identity:** Set your application name and URL (e.g., `Dineflo POS`).
   - **Environment Mode:** Toggle between `local` (Development) and `production`.
   - **App Timezone:** Choose your global timezone (Default: `UTC`).
   - **Smart Reverb Logic:** The installer automatically parses your `APP_URL` to configure **Laravel Reverb (WebSocket)** settings (`REVERB_HOST`, `REVERB_SCHEME: http`).
   - **Smart Debug:** Selecting `local` mode will automatically enable `APP_DEBUG=true` for easier development.
4. **Step 3: License Activation:** 
   - Enter your **3FLO License Key**.
   - The system will verify your instance with the Central License Hub.
5. **Step 4: Database Setup:** The installer will ask for database credentials.
   - Default Laragon: Host: `127.0.0.1`, User: `root`, Password: (blank), DB Name: `dineflo`.
6. **Step 5: Migrations & Finalization:**
   - The installer runs database migrations and seeds.
   - **`APP_INSTALLED`** will be automatically set to `true` in your `.env` and an `installed.lock` file will be created.

---

## ⚙️ Development Highlights

### Real-time Features (WebSockets)
To test features like **Waiter Call** or **Real-time Notifications**, start the Laravel Reverb server:
```bash
php artisan reverb:start
```

### Background Tasks
To test license pings or automated emails locally:
```bash
php artisan schedule:work
```

---

## 🆘 Troubleshooting & Reset

### How to reset the installation?
If you want to re-run the Installation Wizard from Step 1:
1. Delete the lock file: `rm storage/installed.lock`
2. Update `.env` file: `APP_INSTALLED=false`
3. Clear cache: `php artisan optimize:clear`

### Database already exists?
In **Step 4** of the wizard, if the database `dineflo` already exists, choose **"Execute Deployment"** and the installer will safely perform a `migrate:fresh` to reset your local database state.

### Useful Commands
- `php artisan optimize:clear` - Clear all cache after manual `.env` changes.
- `php artisan storage:link` - Ensure public assets are linked (Installer handles this, but good to know).
