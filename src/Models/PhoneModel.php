<?php
namespace ContactsAgenda\Models;

use ContactsAgenda\Config\Database;
use PDO;

class PhoneModel {
    private $pdo;

    /**
     * Constructor
     * Initializes the PDO database connection
     */
    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Add a phone to a contact
     * @param int $contactId
     * @param string $phone
     * @return int ID of the newly added phone
     * @throws Exception on failure
     */
    public function add($contactId, $phone) {
        $stmt = $this->pdo->prepare("INSERT INTO phones (contact_id, phone) VALUES (?, ?)");
        try {
            $stmt->execute([$contactId, $phone]);
            return (int) $this->pdo->lastInsertId(); // retorna o ID numérico
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add phone: " . $e->getMessage());
        }
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
    * Executes a DELETE query and returns true if a record was deleted, false otherwise
    * @param int $id ID of the phone record
    * @return bool True if a record was deleted, false on failure or if no record matched
    */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM phones WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
