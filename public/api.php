<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use ContactsAgenda\Controllers\ContactController;
use ContactsAgenda\Models\Contact;
use ContactsAgenda\Models\Phone;
use ContactsAgenda\Helpers\ContactService;
use ContactsAgenda\Helpers\PhoneService;
use ContactsAgenda\Helpers\Logger;

// Permite requisições CORS (para Postman ou frontend externo)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Inicializa modelos e services
$contactModel = new Contact();
$phoneModel = new Phone();
$contactService = new ContactService($contactModel);
$phoneService = new PhoneService($phoneModel);
$logger = new Logger();

// Lê o método e dados
$method = $_GET['method'] ?? '';
$id = $_GET['id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

// Resposta padrão
$response = ['status' => 'error', 'data' => null, 'message' => 'Unknown method'];

try {
    switch ($method) {
        case 'list':
            $page = (int)($_GET['page'] ?? 1);
            $perPage = 10;
            $allContacts = $contactModel->getAll(); // você pode implementar getPaginated
            $total = count($allContacts);
            $contacts = array_slice($allContacts, ($page-1)*$perPage, $perPage);

            // Inclui telefones
            foreach ($contacts as &$c) {
                $c['phones'] = $phoneModel->getByContact($c['id']);
            }

            $response = ['status' => 'success', 'data' => $contacts, 'total' => $total];
            break;

        case 'create':
            $errors = $contactService->validate($data);
            if (!empty($errors)) {
                $response = ['status' => 'error', 'message' => $errors];
                break;
            }

            $id = $contactService->save($data);
            $phoneService->syncPhones($id, $data['phones'] ?? []);
            $response = ['status' => 'success', 'data' => ['id' => $id]];
            break;

        case 'update':
            if (!$id) throw new Exception("ID is required for update");
            $data['id'] = $id;
            $errors = $contactService->validate($data);
            if (!empty($errors)) {
                $response = ['status' => 'error', 'message' => $errors];
                break;
            }

            $contactService->save($data);
            $phoneService->syncPhones($id, $data['phones'] ?? []);
            $response = ['status' => 'success', 'data' => ['id' => $id]];
            break;

        case 'delete':
            if (!$id) throw new Exception("ID is required for delete");
            $contactModel->delete($id);
            $response = ['status' => 'success', 'data' => ['id' => $id]];
            break;

        default:
            $response = ['status' => 'error', 'message' => 'Invalid method'];
            break;
    }
} catch (\Exception $e) {
    $logger->error("API Error: " . $e->getMessage());
    $response = ['status' => 'error', 'message' => 'Server error'];
}

echo json_encode($response);
