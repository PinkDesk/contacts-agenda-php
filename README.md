# Contacts Agenda SPA

## Overview
This project is a Contacts Management application for sales teams. Users can add, edit, delete, and list contacts with multiple phone numbers. The application is built in PHP for backend and Vue.js for frontend as a Single Page Application (SPA). It demonstrates professional practices, including API integration, pagination, and form validation.

## Features
- Create contacts (name, email, address, multiple phones)
- Edit existing contacts
- Delete contacts or phone numbers
- Paginated and sortable contact list
- Form validation (email format, required fields, phone format)
- Dark clean theme with responsive design
- API endpoints returning JSON (optional for SPA integration)

## Technologies Used
- Backend: PHP (PSR-4 structure)
- Database: MySQL or SQLite
- Frontend: Vanilla Vue.js 3 (no build tools)
- CSS: Custom Dark Clean theme
- HTTP API: `api.php` handles CRUD operations

## Folder Structure
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
├── vendor/                          # Composer dependencies
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
└── README.md                        # Project documentation

## Installation
1. Clone the repository:

bash
git clone <repo-url>
cd contacts-agenda-php

2. Set up a PHP server (built-in or via XAMPP/WAMP):
php -S localhost:8000 -t public

3. Make sure the database is configured in your PHP code (config.php or similar) and migrated.

## Usage
This project demonstrates two implementations of the same Contacts Agenda functionality to showcase different approaches:

1. Vue.js SPA (Single Page Application)
- Open the SPA in a browser: http://localhost:8000/app/index.html
- The interface is fully dynamic:
- - Paginated and sortable contacts list
- - Form with validation and dynamic phone fields
- - Success and error messages displayed above the form
- All CRUD operations are performed through the api.php endpoints returning JSON.

2. PHP MVC (Server-Side Rendered)
- Open the PHP-rendered version in a browser: http://localhost:8000/index.php?page=1
- This version uses classic MVC patterns:
- - ContactController.php handles requests
- - Views folder contains the HTML templates for form and list
- - Messages (success/error) are displayed on the page after redirect
- - Pagination is implemented server-side
- Demonstrates backend-only rendering without JavaScript SPA.

## API Endpoints
- GET /api.php?method=list&page=1 → List contacts (paginated)
- POST /api.php?method=create → Create contact
- POST /api.php?method=update&id={id} → Update contact
- DELETE /api.php?method=delete&id={id} → Delete contact

## Notes
- Validation is performed in the frontend and backend.
- All code, comments, and documentation are in English.
- Designed to be professional and ready for demo purposes.