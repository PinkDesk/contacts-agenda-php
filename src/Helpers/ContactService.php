<?php
namespace ContactsAgenda\Helpers;

use ContactsAgenda\Models\Contact;

class ContactService {
    private $contactModel;

    public function __construct(Contact $contactModel) {
        $this->contactModel = $contactModel;
    }

    public function validate(array $data): array {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors[] = "Name is required.";
        }

        if (empty(trim($data['email'] ?? ''))) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email format is invalid.";
        }

        // Checa duplicidade
        $existing = $this->contactModel->findByEmail($data['email'] ?? '');
        if ($existing && (!isset($data['id']) || $existing['id'] != $data['id'])) {
            $errors[] = "Email already exists.";
        }

        return $errors;
    }

    public function save(array $data): int {
        $id = $data['id'] ?? null;

        if ($id) {
            $this->contactModel->update($id, $data['name'], $data['email'], $data['address']);
        } else {
            $this->contactModel->create($data['name'], $data['email'], $data['address']);
            $id = $this->contactModel->findByEmail($data['email'])['id'];
        }

        return $id;
    }
}
