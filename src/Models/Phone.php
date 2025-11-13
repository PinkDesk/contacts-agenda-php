<?php
namespace ContactsAgenda\Models;

use ContactsAgenda\Config\Database;
use PDO;

class Phone {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function add($contact_id, $phone) {
        $stmt = $this->pdo->prepare("INSERT INTO phones (contact_id, phone) VALUES (?, ?)");
        return $stmt->execute([$contact_id, $phone]);
    }

    public function getByContact($contact_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM phones WHERE contact_id = ?");
        $stmt->execute([$contact_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM phones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
