<?php
namespace ContactsAgenda\Models;

use ContactsAgenda\Config\Database;
use PDO;

class Contact {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function create($name, $email, $address) {
        $stmt = $this->pdo->prepare("INSERT INTO contacts (name, email, address) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $address]);
    }

    public function getAll($offset = 0, $limit = null)
    {
        $sql = "SELECT * FROM contacts ORDER BY name ASC";
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

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $email, $address) {
        $stmt = $this->pdo->prepare("UPDATE contacts SET name = ?, email = ?, address = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $address, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPaginated($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM contacts ORDER BY name ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Método para contar todos os contatos
    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM contacts");
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
