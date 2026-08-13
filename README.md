# Car Hire Website + Admin Dashboard (Laravel)

A complete car hire / logistics website with a dashboard where you change everything
yourself — no developer needed after setup.

## What's in the box

**Public website**
- Homepage: video/image hero with a booking form built in, filterable fleet grid, services, stats, city cards, review carousel, FAQ accordion, blog teaser, contact
- Blog listing with category filters and search, plus individual article pages
- Location directory + **auto-generated landing pages** (every area × every service = its own URL, e.g. `/locations/moving-truck-lekki-phase-1`)
- City pages at `/car-rental-lagos`, `/car-rental-abuja`, etc.
- Standalone service pages at `/services/moving-truck`
- Editable pages (Privacy, Terms, anything you add)
- `sitemap.xml` generated from your live content
- Booking form saves to the dashboard, then hands off to WhatsApp

**Admin dashboard** at `/admin`
- Bookings inbox with statuses (new → contacted → confirmed → completed), internal notes, one-click WhatsApp/call the client
- Fleet + categories, cities, areas, services, blog, reviews, FAQs, pages
- Site settings: business details, phone/WhatsApp, homepage copy, statistics, social links, SEO defaults, analytics snippet
- Image uploads on every content type

## The key idea: location pages multiply

Areas and services are separate lists. The site pairs them automatically:

- 30 areas × 10 services = **300 location pages**, live instantly
- Add one new area → 10 new pages appear
- Add one new service → every area gains a page

You never write these pages by hand.

---

## Install

You need PHP 8.2+, Composer and MySQL.

### 1. Create a fresh Laravel project

```bash
composer create-project laravel/laravel carhire
cd carhire
```

### 2. Copy this package over it

Copy everything from this folder into the project root, overwriting when asked.

```bash
cp -r /path/to/naija-car-hire/* .
```

Files that intentionally replace Laravel's defaults: `bootstrap/app.php`,
`bootstrap/providers.php`, `routes/web.php`, `app/Http/Controllers/Controller.php`.
Everything else is new.

### 3. Configure

Edit `.env`:

```
APP_NAME="Your Company Name"
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Used once, by the seeder, to create your dashboard login
ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@yourdomain.com
ADMIN_PASSWORD=pick-a-strong-password
```

### 4. Build the database and go

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://localhost:8000` for the site and `http://localhost:8000/admin` to sign in
with the `ADMIN_EMAIL` / `ADMIN_PASSWORD` above.

**Change that password after your first sign-in.**

---

## First things to do in the dashboard

1. **Site settings** — business name, phone, WhatsApp number, email, addresses. The
   WhatsApp number drives every Book Now button on the site.
2. **Site settings → Homepage** — hero heading, subheading, hero image or video URL.
3. **Fleet** — replace the sample vehicles with yours and upload real photos. Vehicles
   with no day rate show your "price note" instead, which is how you do
   "Contact us for a quote".
4. **Cities and Areas** — the seeded list is a starting point. Delete what you don't
   cover, add what you do.
5. **Services** — rename or remove any you don't offer. Each one you keep generates a
   page for every area.
6. **Reviews, FAQs, Blog** — replace the placeholder text with your own.

---

## Deploying to Hostinger cPanel

See `docs/DEPLOY-HOSTINGER.md` for the step-by-step version, including the
`public_html` arrangement and the `storage` symlink workaround.

## Day-to-day editing

See `docs/EDITING-GUIDE.md` — written for whoever runs the business, not for a
developer.

---

## Adding a new content type later

Everything in the dashboard is generated from `config/admin.php`. To add a section:

1. Create the model and migration.
2. Add an entry to `config/admin.php` describing its columns and fields.

The list screen, the form, validation and image uploads all appear automatically.
No controller, no views.

## Design notes

- Colours and type live at the top of `public/assets/css/site.css` as CSS variables.
  Change `--green`, `--amber` and `--asphalt` there to rebrand the whole site.
- Fonts are Archivo (headings), Public Sans (body) and Roboto Mono (rates and labels),
  loaded from Google Fonts.
- No build step. No npm. Edit the CSS and refresh.
