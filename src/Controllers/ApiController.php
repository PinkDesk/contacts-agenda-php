<?php
namespace ContactsAgenda\Controllers;

use ContactsAgenda\Models\ContactModel;
use ContactsAgenda\Models\PhoneModel;

class ApiController
{
    private $contactModel;
    private $phoneModel;

    /**
     * Constructor
     * Initializes models and starts session if needed.
     * Sets JSON response header.
     */
    public function __construct()
    {
        $this->contactModel = new ContactModel();
        $this->phoneModel   = new PhoneModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * GET /api/contacts
     * List all contacts with basic pagination and their phones.
     */
    public function list($params = [])
    {
        $page    = isset($params['page']) ? (int)$params['page'] : 1;
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        // Fetch paginated contacts
        $contacts = $this->contactModel->getAll($offset, $perPage);

        // Attach phone numbers to each contact
        foreach ($contacts as &$c) {
            $c['phones'] = $this->phoneModel->getByContact($c['id']);
        }

        echo json_encode([
            'status'   => 'success',
            'data'     => $contacts,
            'page'     => $page,
            'per_page' => $perPage
        ]);
    }

    /**
     * POST /api/contacts
     * Create a new contact and associated phone numbers.
     */
    public function create($data)
    {
        $name    = trim($data['name'] ?? '');
        $email   = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $phones  = $data['phones'] ?? [];

        // Validate input
        $errors = $this->validate($name, $email, $phones);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Check for duplicate email
        $existing = $this->contactModel->findByEmail($email);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'errors' => ['Email already exists']]);
            return;
        }

        // Create contact
        $this->contactModel->create($name, $email, $address);
        $id = $this->contactModel->findByEmail($email)['id'];

        // Add phone numbers
        foreach ($phones as $phone) {
            $phone = trim($phone);
            if ($phone !== '') {
                $this->phoneModel->add($id, $phone);
            }
        }

        echo json_encode(['status' => 'success', 'data' => ['id' => $id]]);
    }

    /**
     * PUT /api/contacts/{id}
     * Update a contact and its phone numbers.
     */
    public function update($id, $data)
    {
        $contact = $this->contactModel->findById($id);
        if (!$contact) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'errors' => ['Contact not found']]);
            return;
        }

        $name    = trim($data['name'] ?? '');
        $email   = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $phones  = $data['phones'] ?? [];

        // Validate input (including duplicate email check)
        $errors = $this->validate($name, $email, $phones, $id);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Update contact info
        $this->contactModel->update($id, $name, $email, $address);

        // Remove existing phones
        foreach ($this->phoneModel->getByContact($id) as $p) {
            $this->phoneModel->delete($p['id']);
        }

        // Add new phones
        foreach ($phones as $phone) {
            $phone = trim($phone);
            if ($phone !== '') {
                $this->phoneModel->add($id, $phone);
            }
        }

        echo json_encode(['status' => 'success', 'data' => ['id' => $id]]);
    }

    /**
     * DELETE /api/contacts/{id}
     * Delete a contact by ID.
     */
    public function delete($id)
    {
        $contact = $this->contactModel->findById($id);
        if (!$contact) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'errors' => ['Contact not found']]);
            return;
        }

        $this->contactModel->delete($id);
        echo json_encode(['status' => 'success']);
    }

    /**
     * Validate contact data
     * @param string $name
     * @param string $email
     * @param array $phones
     * @param int|null $id Optional contact ID for updates
     * @return array List of errors
     */
    private function validate($name, $email, $phones, $id = null)
    {
        $errors = [];

        // Basic validation
        if (!$name)  $errors[] = "Name is required";
        if (!$email) $errors[] = "Email is required";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is invalid";
        }

        // Validate phone formats
        foreach ($phones as $index => $phone) {
            $phone = trim($phone);
            if ($phone !== '' && !preg_match('/^\+?[0-9\s\-]+$/', $phone)) {
                $errors[] = "Phone #" . ($index + 1) . " has invalid format";
            }
        }

        // Check for duplicate email when updating
        if ($id) {
            $existing = $this->contactModel->findByEmail($email);
            if ($existing && $existing['id'] != $id) {
                $errors[] = "Email already exists";
            }
        }

        return $errors;
    }
}
