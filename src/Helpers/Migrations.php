<?php
require __DIR__ . '/../../vendor/autoload.php';

use ContactsAgenda\Config\Database;

$pdo = Database::getConnection();

// Cria tabela contacts
$pdo->exec("
CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    address TEXT
);
");

// Cria tabela phones
$pdo->exec("
CREATE TABLE IF NOT EXISTS phones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    contact_id INTEGER NOT NULL,
    phone TEXT NOT NULL,
    FOREIGN KEY(contact_id) REFERENCES contacts(id) ON DELETE CASCADE
);
");

echo "Tables created successfully!";
