<?php
session_start();

// Enable full error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use ContactsAgenda\Controllers\ContactController;

/**
 * Initialize the main controller
 */
$controller = new ContactController();

/**
 * Read the requested action and optional ID from the query parameters
 */
$action = $_GET['action'] ?? 'index';
$id     = $_GET['id'] ?? null;

/**
 * Route the request to the appropriate controller method
 */
switch ($action) {
    
    // Display the contact form (for create or edit)
    case 'form':
        $controller->form($id);
        break;
    
    // Save contact data (insert or update)
    case 'save':
        $controller->save($_POST);
        break;
    
    // Delete a contact by ID
    case 'delete':
        $controller->delete($id);
        break;
    
    // Default: display the contacts list
    default:
        $controller->index();
        break;
}
