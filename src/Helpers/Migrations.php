<?php
require __DIR__ . '/../../vendor/autoload.php';

use ContactsAgenda\Config\Database;

// Get PDO connection from Database config
$pdo = Database::getConnection();

// ---------------------------
// Create 'contacts' table
// ---------------------------
$pdo->exec("
CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- Auto-increment primary key
    name TEXT NOT NULL,                    -- Contact name (required)
    email TEXT NOT NULL UNIQUE,            -- Contact email (required, must be unique)
    address TEXT                           -- Optional address field
);
");

// ---------------------------
// Create 'phones' table
// ---------------------------
$pdo->exec("
CREATE TABLE IF NOT EXISTS phones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,  -- Auto-increment primary key
    contact_id INTEGER NOT NULL,           -- Foreign key linking to 'contacts' table
    phone TEXT NOT NULL,                   -- Phone number (required)
    FOREIGN KEY(contact_id) REFERENCES contacts(id) ON DELETE CASCADE  -- Cascade delete phones when contact is deleted
);
");

echo "Tables created successfully!";
