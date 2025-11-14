<?php
namespace ContactsAgenda\Models;

use ContactsAgenda\Config\Database;
use PDO;

class Phone {
    private $pdo;

    /**
     * Constructor
     * Initializes the PDO database connection
     */
    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Add a new phone number for a contact
     * @param int $contact_id ID of the contact
     * @param string $phone Phone number
     * @return bool True on success, false on failure
     */
    public function add($contact_id, $phone) {
        $stmt = $this->pdo->prepare("INSERT INTO phones (contact_id, phone) VALUES (?, ?)");
        return $stmt->execute([$contact_id, $phone]);
    }

    /**
     * Retrieve all phone numbers associated with a contact
     * @param int $contact_id ID of the contact
     * @return array Array of phone records
     */
    public function getByContact($contact_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM phones WHERE contact_id = ?");
        $stmt->execute([$contact_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a phone record by its ID
     * @param int $id ID of the phone record
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM phones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
