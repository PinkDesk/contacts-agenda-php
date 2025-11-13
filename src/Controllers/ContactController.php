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

    public function __construct() {
        $this->contactModel = new Contact();
        $this->phoneModel = new Phone();
        $this->contactService = new ContactService($this->contactModel);
        $this->phoneService = new PhoneService($this->phoneModel);
        $this->logger = new Logger();
    }

    // List contacts
    public function index()
    {
        // Recupera mensagens da sessão
        $messages = $_SESSION['messages'] ?? [];
        unset($_SESSION['messages']); // Limpa após exibir

        // Paginação
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $totalContacts = $this->contactModel->countAll();
        $totalPages = ceil($totalContacts / $perPage);
        $offset = ($page - 1) * $perPage;

        $contacts = $this->contactModel->getAll($offset, $perPage);

        include __DIR__ . '/../Views/contacts/list.php';
    }


    // Show create/edit form
    public function form($id = null) {
        $contact = null;
        $phones = [];
        if ($id) {
            $contact = $this->contactModel->findById($id);
            $phones = $this->phoneModel->getByContact($id);
        }
        include __DIR__ . '/../Views/contacts/form.php';
    }

    public function save($data) {
        $errors = $this->contactService->validate($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header("Location: index.php?action=form&id=" . ($data['id'] ?? ''));
            exit;
        }

        try {
            $id = $this->contactService->save($data);
            $this->phoneService->syncPhones($id, $data['phones'] ?? []);
            $_SESSION['success'] = "Contact saved successfully!";
        } catch (\Exception $e) {
            $this->logger->error("Failed to save contact: " . $e->getMessage());
            $_SESSION['errors'] = ["An unexpected error occurred."];
        }

        header("Location: index.php");
        exit;
    }
}
