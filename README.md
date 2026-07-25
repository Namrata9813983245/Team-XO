# AgroSense — Rule-Based Crop Recommendation System

A complete PHP + SQL web app that recommends crops using a transparent,
editable **rule-based engine** (no black-box ML) driven by soil type,
temperature, humidity, and soil moisture.

## Quick start

Requirements: PHP 8+ with the `pdo_sqlite` and `gd` extensions (both are
bundled with almost every PHP install).

```bash
cd cropsys
php -S localhost:8000
```

Open **http://localhost:8000** in your browser. The SQLite database
(`data/cropsys.sqlite`) and demo data are created automatically on first
request — nothing else to configure.

### Demo logins
| Role  | Email                  | Password  |
|-------|------------------------|-----------|
| Admin | admin@cropsys.local    | admin123  |
| User  | farmer@cropsys.local   | user123   |

Logging in automatically routes admins to `/admin/dashboard.php` and
regular users to `/user/home.php`.

## Switching to MySQL

The app uses PDO, so moving to MySQL only means changing one file:
`config/db.php`. Replace the SQLite DSN with:

```php
new PDO("mysql:host=localhost;dbname=cropsys;charset=utf8mb4", $user, $pass)
```

Then export `data/cropsys.sqlite`'s structure/data (or re-run the
`install_schema()` logic in `config/schema.php` against MySQL) and you're
done — every query in the app already uses portable PDO/prepared
statements.

## What's included

- **Auth** — register/login/logout with hashed passwords and role-based
  redirect (admin vs. user).
- **Rule-based recommendation engine** (`includes/functions.php ->
  recommend_crops()`) — scores every crop against submitted soil type,
  temperature, humidity, and moisture using clear IF/THEN range rules, and
  returns ranked matches with a plain-language reason for each.
- **Dynamic form fields** — admins can add/edit/deactivate/remove the
  inputs shown on the recommendation form (`admin/fields.php`), no code
  changes required.
- **Live IoT panel** — `api/live_data.php` simulates a sensor feed (swap
  this for a real MQTT/REST bridge in production) and both the user home
  page and the "Load Live Sensor Data" button on the recommendation form
  pull from it live.
- **User dashboard** — home page (live gauges + articles/photos), crop
  recommendation form, history (with crop photos), and settings (profile
  picture, name, password).
- **Admin dashboard** — stats, crop CRUD with image upload, dynamic field
  manager, user management (promote/demote/delete), full recommendation
  history, article publishing, contact-message inbox, and site-wide
  settings (site name, About Us copy, contact info).
- **About Us** (nav bar) and **Contact Us** (footer, with a working form
  that saves to the database and shows up in the admin inbox).

## Notes on images

Crop and article photos default to keyword-matched stock photos via
loremflickr.com so the catalog looks populated out of the box. Admins can
replace any of them with their own upload at any time from the Crops or
Articles screens.
