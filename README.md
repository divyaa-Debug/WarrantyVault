# WarrantyVault 🔐

A modern, streamlined web application for managing product warranties, invoices, serial numbers, and expiry alerts.

---

## 🚀 Features

- **Smart Dashboard**: Real-time snapshot of active, expiring soon (within 30 days), and expired warranties.
- **Product Management**: Register and organize products with categories, purchase dates, warranty duration, prices, store names, and custom notes.
- **Auto Expiration Calculation**: Automatically calculates warranty expiry dates based on purchase date and warranty duration.
- **User Authentication**: Secure registration and login with bcrypt password hashing and session handling.
- **Responsive Interface**: Human-crafted modern design with dark-mode accents and mobile-friendly layouts.

---

## 🛠️ Local Development (XAMPP)

1. Clone or place the repository inside your XAMPP web root:
   ```bash
   C:\xampp\htdocs\warrantyvault
   ```
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`, create a database named `warrantyvault`, and import `database.sql`.
4. Open the project in your browser:
   ```
   http://localhost/warrantyvault/
   ```

---

## ☁️ Cloud Deployment (Render / Railway)

This project includes Docker support and smart environment variable detection for cloud hosting.

### Option A: Deploy to Render (Free)

1. **Push your repository** to GitHub.
2. Go to [Render Dashboard](https://dashboard.render.com/) and click **New +** → **Web Service**.
3. Select your GitHub repository (`divyaa-Debug/WarrantyVault`).
4. Render will automatically detect the `Dockerfile`.
5. Under **Environment Variables**, add your cloud MySQL details:
   - `DB_HOST`: Your cloud database host (e.g. from Aiven, TiDB, or Railway)
   - `DB_USER`: Database username
   - `DB_PASSWORD`: Database password
   - `DB_NAME`: Database name (e.g. `warrantyvault`)
   - `DB_PORT`: Database port (default `3306`)
   *(Alternatively, provide `MYSQL_URL` if using a single connection string).*
6. Click **Create Web Service**.

### Option B: Deploy to Railway

1. Go to [Railway](https://railway.app/) and create a **New Project**.
2. Select **Deploy from GitHub repo** → select `WarrantyVault`.
3. Add a **MySQL Database** service to the same Railway project.
4. Import `database.sql` into your Railway MySQL service.
5. In your Web Service settings, add a reference variable: `MYSQL_URL` = `${{MySQL.MYSQL_URL}}`.
6. Railway will build and deploy your containerized app with full persistence.

---

## 🗄️ Database Schema

The database schema is located in [`database.sql`](./database.sql).
Tables included:
- `users`: User authentication records.
- `products`: Tracked warranty items linked to each user.
