# Deploy MediQueue OPD for sharing

Share **`/promo`** on WhatsApp once you have a public HTTPS URL.

## 1. Environment (production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
# ... your production DB credentials
```

Then:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Point your web server document root to **`public/`**.

---

## 2. Quick share today (ngrok)

While developing locally:

```bash
php artisan serve
# In another terminal:
ngrok http 8000
```

Copy the `https://xxxx.ngrok-free.app` URL into `.env`:

```env
APP_URL=https://xxxx.ngrok-free.app
```

Restart `php artisan serve`, then share:

```
https://xxxx.ngrok-free.app/promo
```

---

## 3. Common hosts

| Host | Notes |
|------|--------|
| **VPS** (DigitalOcean, Hetzner) | Nginx + PHP 8.2 + MySQL, cheapest long-term |
| **Shared hosting** | Upload files, set root to `public/` |
| **Laravel Forge / Ploi** | Easiest VPS management |

Minimum requirements: PHP 8.2+, MySQL 8+, Composer, Node (build once).

---

## 4. After deploy

1. Visit `https://your-domain.com/promo`
2. Tap **Share demo via WhatsApp**
3. Use templates in [WHATSAPP_PROMOTION.md](WHATSAPP_PROMOTION.md)

Admin demo: `/admin/login` — `admin@mediqueue.local` / `password`

---

## 5. Security before going public

- Change admin password after first login (Module 9)
- Set `APP_DEBUG=false`
- Use HTTPS only
- Restrict admin routes by IP if needed
