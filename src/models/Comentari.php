<?php
require_once 'Database.php';

class Comentari {
    private $conn;
    private $table = 'comentaris';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear un comentari
    public function create($usuari_id, $cim_id, $text) {
        $query = "INSERT INTO $this->table (usuari_id, cim_id, text, data) VALUES (:usuari_id, :cim_id, :text, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuari_id', $usuari_id);
        $stmt->bindParam(':cim_id', $cim_id);
        $stmt->bindParam(':text', $text);
        return $stmt->execute();
    }

    // Llegir comentaris d’un cim
    public function getByCim($cim_id) {
        $query = "SELECT c.text, c.data, u.nomUsuari 
                  FROM $this->table c 
                  JOIN usuaris u ON c.usuari_id = u.id 
                  WHERE c.cim_id = :cim_id
                  ORDER BY c.data DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cim_id', $cim_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar comentari (només autor o admin)
    public function delete($id, $usuari_id) {
        $query = "DELETE FROM $this->table WHERE id = :id AND usuari_id = :usuari_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuari_id', $usuari_id);
        return $stmt->execute();
    }
}
?>
