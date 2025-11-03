<?php
require_once 'Database.php';

class Like {
    private $conn;
    private $table = 'likes';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Afegir un like
    public function addLike($usuari_id, $cim_id) {
        $query = "INSERT INTO $this->table (usuari_id, cim_id, data) VALUES (:usuari_id, :cim_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuari_id', $usuari_id);
        $stmt->bindParam(':cim_id', $cim_id);
        return $stmt->execute();
    }

    // Treure un like
    public function removeLike($usuari_id, $cim_id) {
        $query = "DELETE FROM $this->table WHERE usuari_id = :usuari_id AND cim_id = :cim_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuari_id', $usuari_id);
        $stmt->bindParam(':cim_id', $cim_id);
        return $stmt->execute();
    }

    // Comptar likes d’un cim
    public function countLikes($cim_id) {
        $query = "SELECT COUNT(*) AS total FROM $this->table WHERE cim_id = :cim_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cim_id', $cim_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Comprovar si un usuari ja ha fet like
    public function hasLiked($usuari_id, $cim_id) {
        $query = "SELECT * FROM $this->table WHERE usuari_id = :usuari_id AND cim_id = :cim_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuari_id', $usuari_id);
        $stmt->bindParam(':cim_id', $cim_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
