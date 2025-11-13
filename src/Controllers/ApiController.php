<?php
namespace ContactsAgenda\Controllers;

use ContactsAgenda\Models\Contact;
use ContactsAgenda\Models\Phone;

class ApiController
{
    private $contactModel;
    private $phoneModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
        $this->phoneModel = new Phone();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');
    }

    // GET /api/contacts
    public function list($params = [])
    {
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $contacts = $this->contactModel->getAll($offset, $perPage);
        // Adiciona telefones
        foreach ($contacts as &$c) {
            $c['phones'] = $this->phoneModel->getByContact($c['id']);
        }

        echo json_encode([
            'status' => 'success',
            'data' => $contacts,
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    // POST /api/contacts
    public function create($data)
    {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $phones = $data['phones'] ?? [];

        $errors = $this->validate($name, $email, $phones);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $existing = $this->contactModel->findByEmail($email);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'errors' => ['Email already exists']]);
            return;
        }

        $this->contactModel->create($name, $email, $address);
        $id = $this->contactModel->findByEmail($email)['id'];

        foreach ($phones as $phone) {
            $phone = trim($phone);
            if ($phone !== '') {
                $this->phoneModel->add($id, $phone);
            }
        }

        echo json_encode(['status' => 'success', 'data' => ['id' => $id]]);
    }

    // PUT /api/contacts/{id}
    public function update($id, $data)
    {
        $contact = $this->contactModel->findById($id);
        if (!$contact) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'errors' => ['Contact not found']]);
            return;
        }

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $phones = $data['phones'] ?? [];

        $errors = $this->validate($name, $email, $phones, $id);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $this->contactModel->update($id, $name, $email, $address);

        // Atualiza telefones
        foreach ($this->phoneModel->getByContact($id) as $p) {
            $this->phoneModel->delete($p['id']);
        }
        foreach ($phones as $phone) {
            $phone = trim($phone);
            if ($phone !== '') {
                $this->phoneModel->add($id, $phone);
            }
        }

        echo json_encode(['status' => 'success', 'data' => ['id' => $id]]);
    }

    // DELETE /api/contacts/{id}
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

    // Validação comum
    private function validate($name, $email, $phones, $id = null)
    {
        $errors = [];

        if (!$name) $errors[] = "Name is required";
        if (!$email) $errors[] = "Email is required";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email is invalid";

        foreach ($phones as $index => $phone) {
            $phone = trim($phone);
            if ($phone !== '' && !preg_match('/^\+?[0-9\s\-]+$/', $phone)) {
                $errors[] = "Phone #" . ($index + 1) . " has invalid format";
            }
        }

        // Checa duplicado se for update
        if ($id) {
            $existing = $this->contactModel->findByEmail($email);
            if ($existing && $existing['id'] != $id) {
                $errors[] = "Email already exists";
            }
        }

        return $errors;
    }
}
