# MediQueue OPD

Smart Hospital Queue Management — a production-oriented Laravel SaaS for hospital OPD booking, payments, tokens, QR codes, and live queue tracking.

## Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Views:** Blade
- **CSS:** Tailwind CSS 4
- **JS:** Vanilla ES modules
- **Database:** MySQL 8+ (SQLite for tests)

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8+

## Setup

```bash
# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env (default for local dev)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mediqueue
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seed (Module 2+)
php artisan migrate --seed

# Build assets
npm run build

# Development (server + vite)
composer run dev
```

Visit `http://127.0.0.1:8000`

## Share / promote (WhatsApp)

- **Promo page:** `/promo` — shareable demo + WhatsApp share button
- **Templates:** [docs/WHATSAPP_PROMOTION.md](docs/WHATSAPP_PROMOTION.md)
- **Deploy guide:** [docs/DEPLOY.md](docs/DEPLOY.md)

Set `APP_URL` in `.env` to your public domain before sharing links.

## Admin Access (after Module 9 seed)

- URL: `/admin/login`
- Email: `admin@mediqueue.local`
- Password: `password`

## Architecture

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) for layer responsibilities, service map, and module build order.

## Module Progress

| Module | Status |
|--------|--------|
| 1 — Foundation | ✅ Complete |
| 2 — Database & Models | Pending |
| 3 — Patient Booking | Pending |
| 4 — Live Queue | Pending |
| 5 — Notifications | Pending |
| 6 — Admin Master CRUD | Pending |
| 7 — Slots & Appointments | Pending |
| 8 — Dashboard & Reports | Pending |
| 9 — Auth & QA | Pending |

## Branding

- App: **MediQueue OPD**
- Brand color: `#0f766e` (teal)
- Fonts: DM Sans (UI), Source Serif 4 (display)

## Testing

```bash
php artisan test
```

Tests use SQLite `:memory:` (configured in `phpunit.xml`).

## License

MIT
