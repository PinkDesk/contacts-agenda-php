<?php
namespace ContactsAgenda\Controllers;

use ContactsAgenda\Models\Contact;
use ContactsAgenda\Models\Phone;
use ContactsAgenda\Helpers\ContactService;
use ContactsAgenda\Helpers\PhoneService;
use ContactsAgenda\Helpers\Logger;

class ContactController {
    private $contactModel;
    private $phoneModel;
    private $contactService;
    private $phoneService;
    private $logger;

    /**
     * Constructor
     * Initializes models, services, and logger
     */
    public function __construct() {
        $this->contactModel   = new Contact();
        $this->phoneModel     = new Phone();
        $this->contactService = new ContactService($this->contactModel);
        $this->phoneService   = new PhoneService($this->phoneModel);
        $this->logger         = new Logger();
    }

    /**
     * List all contacts with pagination
     * Loads messages from session if any, then clears them
     */
    public function index() {
        // Retrieve messages from session
        $messages = $_SESSION['messages'] ?? [];
        unset($_SESSION['messages']); // Clear messages after displaying

        // Pagination parameters
        $page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage    = 10;
        $totalContacts = $this->contactModel->countAll();
        $totalPages = ceil($totalContacts / $perPage);
        $offset     = ($page - 1) * $perPage;

        // Fetch paginated contacts
        $contacts = $this->contactModel->getAll($offset, $perPage);

        // Include the view to render the contacts list
        include __DIR__ . '/../Views/contacts/list.php';
    }

    /**
     * Show the create/edit contact form
     * If ID is provided, fetch existing contact and phones
     */
    public function form($id = null) {
        $contact = null;
        $phones  = [];

        if ($id) {
            $contact = $this->contactModel->findById($id);
            $phones  = $this->phoneModel->getByContact($id);
        }

        // Include the form view
        include __DIR__ . '/../Views/contacts/form.php';
    }

    /**
     * Save contact (create or update)
     * Validates input, synchronizes phones, and handles errors
     */
    public function save($data) {
        // Validate form data
        $errors = $this->contactService->validate($data);

        if (!empty($errors)) {
            // Store errors and old input in session to repopulate form
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;

            // Redirect back to form
            header("Location: index.php?action=form&id=" . ($data['id'] ?? ''));
            exit;
        }

        try {
            // Save contact and sync phone numbers
            $id = $this->contactService->save($data);
            $this->phoneService->syncPhones($id, $data['phones'] ?? []);

            // Store success message
            $_SESSION['success'] = "Contact saved successfully!";
        } catch (\Exception $e) {
            // Log unexpected errors
            $this->logger->error("Failed to save contact: " . $e->getMessage());

            // Store generic error message
            $_SESSION['errors'] = ["An unexpected error occurred."];
        }

        // Redirect to contacts list after saving
        header("Location: index.php");
        exit;
    }
}
