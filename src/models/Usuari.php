<?php
require_once 'Database.php';

class Usuari {
    private $conn;
    private $table = 'usuaris';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Registrar usuari nou
    public function register($nomUsuari, $mail, $contrasenya) {
        $hash = password_hash($contrasenya, PASSWORD_DEFAULT);
        $query = "INSERT INTO $this->table (nomUsuari, mail, contrasenya, rol) VALUES (:nomUsuari, :mail, :contrasenya, 'usuari')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nomUsuari', $nomUsuari);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':contrasenya', $hash);
        return $stmt->execute();
    }

    // Iniciar sessió
    public function login($mail, $contrasenya) {
        $query = "SELECT * FROM $this->table WHERE mail = :mail";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuari && password_verify($contrasenya, $usuari['contrasenya'])) {
            return $usuari;
        }
        return false;
    }

    // Obtenir dades d’un usuari
    public function getById($id) {
        $query = "SELECT id, nomUsuari, mail, rol FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Llistar tots els usuaris (només admin)
    public function getAll() {
        $query = "SELECT id, nomUsuari, mail, rol FROM $this->table";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar usuari
    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
