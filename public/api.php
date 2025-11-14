<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use ContactsAgenda\Controllers\ContactController;
use ContactsAgenda\Models\ContactModel;
use ContactsAgenda\Models\PhoneModel;
use ContactsAgenda\Helpers\ContactService;
use ContactsAgenda\Helpers\PhoneService;
use ContactsAgenda\Helpers\Logger;

/**
 * CORS headers (allow external clients such as Postman or a remote frontend)
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

/**
 * Initialize models and services
 */
$contactModel   = new ContactModel();
$phoneModel     = new PhoneModel();
$contactService = new ContactService($contactModel);
$phoneService   = new PhoneService($phoneModel);
$logger         = new Logger();

/**
 * Read input method and data
 */
$method = $_GET['method'] ?? '';
$id     = $_GET['id'] ?? null;
$data   = json_decode(file_get_contents('php://input'), true);

/**
 * Default response
 */
$response = [
    'status'  => 'error',
    'data'    => null,
    'message' => 'Unknown method'
];

try {
    switch ($method) {

        /**
         * List contacts (basic pagination)
         */
        case 'list':
            $page    = (int)($_GET['page'] ?? 1);
            $perPage = 10;

            // Fetch all contacts (replace with getPaginated if implemented)
            $allContacts = $contactModel->getAll();
            $total       = count($allContacts);

            // Apply pagination
            $contacts = array_slice($allContacts, ($page - 1) * $perPage, $perPage);

            // Include phone numbers
            foreach ($contacts as &$c) {
                $c['phones'] = $phoneModel->getByContact($c['id']);
            }

            $response = [
                'status' => 'success',
                'data'   => $contacts,
                'total'  => $total
            ];
            break;

        /**
         * Create a new contact
         */
        case 'create':
            $errors = $contactService->validate($data);

            if (!empty($errors)) {
                $response = ['status' => 'error', 'message' => $errors];
                break;
            }

            $id = $contactService->save($data);
            $phoneService->syncPhones($id, $data['phones'] ?? []);

            $response = [
                'status' => 'success',
                'data'   => ['id' => $id]
            ];
            break;

        /**
         * Update an existing contact
         */
        case 'update':
            if (!$id) {
                throw new Exception("ID is required for update");
            }

            $data['id'] = $id;

            $errors = $contactService->validate($data);
            if (!empty($errors)) {
                $response = ['status' => 'error', 'message' => $errors];
                break;
            }

            $contactService->save($data);
            $phoneService->syncPhones($id, $data['phones'] ?? []);

            $response = [
                'status' => 'success',
                'data'   => ['id' => $id]
            ];
            break;

        /**
         * Delete a contact by ID
         */
        case 'delete':
            if (!$id) {
                throw new Exception("ID is required for delete");
            }

            $contactModel->delete($id);

            $response = [
                'status' => 'success',
                'data'   => ['id' => $id]
            ];
            break;

        /**
         * Invalid method
         */
        default:
            $response = [
                'status'  => 'error',
                'message' => 'Invalid method'
            ];
            break;
    }

} catch (\Exception $e) {

    // Log exception
    $logger->error("API Error: " . $e->getMessage());

    // Standard server error response
    $response = [
        'status'  => 'error',
        'message' => 'Server error'
    ];
}

echo json_encode($response);
