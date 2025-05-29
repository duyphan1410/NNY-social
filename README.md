# 📱 NNY Social Network

A modern image-sharing social network built with **Laravel 10**, **Vite**, and **MySQL**. Users can register, post content (images/videos), like, comment, and manage profiles. The system also includes an admin dashboard for moderation and analytics.

## 🛠️ Tech Stack

- **Backend**: PHP 8.1+, Laravel 10
- **Frontend**: Vite, TailwindCSS
- **Database**: MySQL
- **Media**: Cloudinary (images/videos)
- **Real-time**: Pusher

---

## ☁️ Cloudinary Integration

This project uses [Cloudinary](https://cloudinary.com/) for secure and optimized media storage.

- Images are uploaded and converted to `.webp` for performance.
- Videos are automatically compressed and resized if needed.
- Media links are stored and delivered via Cloudinary CDN.

You must configure the following environment variables in `.env`:

```env
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```
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

## 📄 License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.



