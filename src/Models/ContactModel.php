<?php
namespace ContactsAgenda\Models;

use ContactsAgenda\Config\Database;
use PDO;

class ContactModel {
    private $pdo;

    /**
     * Constructor
     * Initializes the PDO database connection
     */
    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
 * Create a new contact
 * @param string $name Contact name
 * @param string $email Contact email
 * @param string $address Contact address
 * @return int ID of the newly created contact
 * @throws Exception if insert fails
 */
public function create($name, $email, $address) {
    $stmt = $this->pdo->prepare("INSERT INTO contacts (name, email, address) VALUES (?, ?, ?)");

    try {
        $stmt->execute([$name, $email, $address]);
        return (int) $this->pdo->lastInsertId();
    } catch (\PDOException $e) {
        if ($e->getCode() == 23000) { // unique constraint violation
            throw new \Exception('Email already exists');
        }
        throw $e;
    }
}

    /**
     * Get all contacts with optional pagination
     * @param int $offset Offset for pagination
     * @param int|null $limit Number of records to fetch (optional)
     * @return array Array of contacts
     */
    public function getAll($offset = 0, $limit = null)
    {
        $sql = "SELECT * FROM contacts ORDER BY LOWER(name) ASC"; // <-- aqui

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a contact by email
     * @param string $email Email to search
     * @return array|null Contact data or null if not found
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing contact
     * @param int $id Contact ID
     * @param string $name Contact name
     * @param string $email Contact email
     * @param string $address Contact address
     * @return bool True on success, false on failure
     */
    public function update($id, $name, $email, $address) {
        $stmt = $this->pdo->prepare("UPDATE contacts SET name = ?, email = ?, address = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $address, $id]);
    }

    /**
     * Delete a contact by ID
     * @param int $id Contact ID
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Find a contact by ID
     * @param int $id Contact ID
     * @return array|null Contact data or null if not found
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get contacts with pagination
     * @param int $page Page number
     * @param int $perPage Number of records per page
     * @return array Array of contacts
     */
    public function getAllPaginated($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM contacts ORDER BY name ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all contacts
     * @return int Total number of contacts
     */
    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM contacts");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
