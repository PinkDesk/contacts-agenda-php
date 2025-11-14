<?php
namespace ContactsAgenda\Helpers;

use ContactsAgenda\Models\Contact;

class ContactService {
    private $contactModel;

    /**
     * Constructor
     * Injects the Contact model dependency.
     */
    public function __construct(Contact $contactModel) {
        $this->contactModel = $contactModel;
    }

    /**
     * Validate contact data
     * @param array $data Input data from form or API
     * @return array List of validation error messages
     */
    public function validate(array $data): array {
        $errors = [];

        // Validate name
        if (empty(trim($data['name'] ?? ''))) {
            $errors[] = "Name is required.";
        }

        // Validate email presence and format
        if (empty(trim($data['email'] ?? ''))) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email format is invalid.";
        }

        // Check for duplicate email in database
        $existing = $this->contactModel->findByEmail($data['email'] ?? '');
        if ($existing && (!isset($data['id']) || $existing['id'] != $data['id'])) {
            $errors[] = "Email already exists.";
        }

        return $errors;
    }

    /**
     * Save contact data (insert or update)
     * @param array $data Contact information
     * @return int ID of the saved contact
     */
    public function save(array $data): int {
        $id = $data['id'] ?? null;

        if ($id) {
            // Update existing contact
            $this->contactModel->update($id, $data['name'], $data['email'], $data['address']);
        } else {
            // Create new contact
            $this->contactModel->create($data['name'], $data['email'], $data['address']);

            // Retrieve the ID of the newly created contact
            $id = $this->contactModel->findByEmail($data['email'])['id'];
        }

        return $id;
    }
}
