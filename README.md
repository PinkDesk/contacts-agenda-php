# Contacts Agenda SPA

## Overview

This project is a Contacts Management application designed for sales teams. Users can add, edit, delete, and list contacts with multiple phone numbers. The application is implemented in **PHP** for the backend and **Vue.js** for the frontend as a Single Page Application (SPA). It demonstrates professional practices including API integration, pagination, form validation, and unit testing.

## Features

* Create contacts (name, email, address, multiple phones)
* Edit existing contacts
* Delete contacts or phone numbers
* Paginated and sortable contact list
* Form validation (email format, required fields, phone format)
* Dark clean theme with responsive design
* API endpoints returning JSON for SPA integration
* PHPUnit tests for models and controllers

## Technologies Used

* **Backend:** PHP (PSR-4 structure)
* **Database:** MySQL or SQLite
* **Frontend:** Vanilla Vue.js 3 (no build tools)
* **CSS:** Custom Dark Clean theme
* **HTTP API:** `api.php` handles CRUD operations
* **Testing:** PHPUnit 9.5

## Folder Structure

```
contacts-agenda-php/
├── Config/
│   └── Database.php                # Database connection configuration
├── public/
│   ├── app/
│   │   ├── components/             # Vue JS components (ContactForm, ContactList)
│   │   └── index.html              # SPA entry page
│   ├── main.js                      # Main SPA bootstrap
│   ├── api.php                      # PHP API for CRUD
│   └── index.php                    # Optional PHP-rendered version
├── src/
│   ├── Controllers/
│   │   ├── ApiController.php       # API endpoints controller
│   │   └── ContactController.php   # Contact-related logic
│   ├── Helpers/
│   │   ├── ContactService.php
│   │   ├── Logger.php
│   │   ├── Migrations.php
│   │   └── PhoneService.php
│   └── Models/
│       ├── Contact.php
│       └── Phone.php
├── Views/
│   └── contacts/
│       ├── form.php
│       ├── list.php
│       └── layout.php
├── storage/
│   ├── data/
│   │   └── contacts.db
│   └── logs/
├── tests/                           # PHPUnit tests for models and controllers
├── vendor/                          # Composer dependencies
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
└── README.md                        # Project documentation
```

## Installation

1. Clone the repository:

```bash
git clone <repo-url>
cd contacts-agenda-php
```

2. Install dependencies using Composer:

```bash
composer install
```

3. Set up a PHP server (built-in or via XAMPP/WAMP):

```bash
php -S localhost:8000 -t public
```

4. Configure your database in `.env` (or `Config/Database.php`) and run migrations if needed.

---

## Environment Variables

Create a `.env` file based on `.env.example`:

```
DB_DRIVER=sqlite
DB_PATH=storage/data/contacts.db
DEBUG=true
```

* `DB_DRIVER`: `sqlite` or `mysql`
* `DB_PATH`: path to SQLite database (ignored if using MySQL)
* `DEBUG`: `true` to enable detailed error messages

---

## Usage

The project demonstrates two implementations of the same Contacts Agenda functionality:

### 1. Vue.js SPA (Single Page Application)

* Open the SPA in a browser: [http://localhost:8000/app/index.html](http://localhost:8000/app/index.html)
* Fully dynamic interface:

  * Paginated and sortable contacts list
  * Form with validation and dynamic phone fields
  * Success and error messages displayed above the form
* All CRUD operations are performed through `api.php` endpoints returning JSON

### 2. PHP MVC (Server-Side Rendered)

* Open the PHP-rendered version in a browser: [http://localhost:8000/index.php?page=1](http://localhost:8000/index.php?page=1)
* Classic MVC pattern:

  * `ContactController.php` handles requests
  * Views folder contains HTML templates for form and list
  * Messages (success/error) are displayed on the page after redirect
  * Pagination implemented server-side
* Demonstrates backend-only rendering without JavaScript SPA

---

## API Endpoints

* `GET /api.php?method=list&page=1` → List contacts (paginated)
* `POST /api.php?method=create` → Create contact
* `POST /api.php?method=update&id={id}` → Update contact
* `DELETE /api.php?method=delete&id={id}` → Delete contact

---

## Running Tests

This project includes PHPUnit tests for models and controllers.

1. Install dev dependencies (if not already):

```bash
composer install --dev
```

2. Run all tests:

```bash
vendor/bin/phpunit
```

3. Run a specific test file:

```bash
vendor/bin/phpunit tests/Models/ContactModelTest.php
```

4. Notes:

* Tests use temporary or in-memory database for isolation.
* `UNIT_TEST` constant is defined to avoid redirects during controller tests.

---

## Notes

* Validation is performed both on the frontend and backend.
* All code, comments, and documentation are in English.
* Designed to be professional and ready for demo or portfolio purposes.
