# 🕸️ NNY Social Network

A modern image-based social network built with **Laravel 10**, **Vite**, and **MySQL**. Users can register, log in, post content (images/videos), interact via likes and comments, manage profiles, and more. It also includes an admin dashboard for managing users and posts.

## 📦 Tech Stack

- PHP 8.1+, Laravel 10
- MySQL
- Vite (for frontend assets)
- TailwindCSS
- Cloudinary (for media storage)
- Firebase (notifications)
- Pusher (real-time features)

---

## 🚀 Getting Started

### 1. Clone the project

```bash
git clone https://github.com/duyphan1410/NNY-social.git
cd NNY-social
````

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Setup environment file

```bash
cp .env.example .env
php artisan key:generate
```

Then configure the `.env` file with your own:

* `APP_URL`
* `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
* `CLOUDINARY_*`
* `MAIL_*`
* `PUSHER_*`

### 4. Setup database

```bash
php artisan migrate --seed
```

### 5. Compile frontend assets

```bash
npm run build
```

### 6. Serve the application

```bash
php artisan serve
```

Visit the app at `http://127.0.0.1:8000`.

---

## 🧪 Sample Accounts

Seeder will create sample users automatically. If not, you can register manually.

---

## 🔐 Important Notes

* **Do not include your `.env` file** when submitting the project.
* Including `vendor/` or `node_modules/` is optional (based on submission rules and size limit).
* Frontend uses Vite, so make sure assets are built before running the server.

---

## 📁 Project Structure

```
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── .env.example
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

---

## 👤 Author

**Duy Phan** – [GitHub Profile](https://github.com/duyphan1410)

---



