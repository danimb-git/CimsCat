<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe Comentari
 * Model per gestionar els comentaris de les excursions
 */
class Comentari {
    // Connexió a la base de dades i nom de la taula
    private $conn;
    private $table = 'comentari';

    // Propietats de l'objecte Comentari
    public $id;
    public $contingut;
    public $data;
    public $id_excursio;
    public $id_usuari;
    public $created_at;

    /**
     * Constructor: inicialitza la connexió
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * CREATE - Crear un nou comentari
     * @return bool|int ID del comentari creat o false si hi ha error
     */
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (contingut, data, id_excursio, id_usuari) 
                  VALUES 
                  (:contingut, CURDATE(), :id_excursio, :id_usuari)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Netegem el contingut per evitar XSS
            $contingut_net = htmlspecialchars(strip_tags($this->contingut));
            
            // Vincular paràmetres
            $stmt->bindParam(':contingut', $contingut_net);
            $stmt->bindParam(':id_excursio', $this->id_excursio, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return $this->id;
            }
            return false;
            
        } catch(PDOException $e) {
            echo "Error al crear el comentari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ - Obtenir tots els comentaris d'una excursió amb info de l'usuari
     * @param int $id_excursio ID de l'excursió
     * @return array Array amb tots els comentaris
     */
    public function getByExcursio($id_excursio) {
        $query = "SELECT c.*, u.nom_usuari, u.nom, u.cognom
                  FROM {$this->table} c
                  INNER JOIN usuari u ON c.id_usuari = u.id
                  WHERE c.id_excursio = :id_excursio
                  ORDER BY c.created_at DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_excursio', $id_excursio, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir els comentaris: " . $e->getMessage();
            return [];
        }
    }

    /**
     * READ - Obtenir un comentari per ID
     * @param int $id ID del comentari
     * @return array|false Array amb les dades del comentari o false si no existeix
     */
    public function getById($id) {
        $query = "SELECT c.*, u.nom_usuari, u.nom, u.cognom
                  FROM {$this->table} c
                  INNER JOIN usuari u ON c.id_usuari = u.id
                  WHERE c.id = :id 
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                $this->id = $result['id'];
                $this->contingut = $result['contingut'];
                $this->data = $result['data'];
                $this->id_excursio = $result['id_excursio'];
                $this->id_usuari = $result['id_usuari'];
                $this->created_at = $result['created_at'];
            }
            
            return $result;
            
        } catch(PDOException $e) {
            echo "Error al obtenir el comentari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ - Obtenir comentaris d'un usuari concret
     * @param int $id_usuari ID de l'usuari
     * @return array Array amb els comentaris de l'usuari
     */
    public function getByUsuari($id_usuari) {
        $query = "SELECT c.*, e.titol as titol_excursio, cim.nom as nom_cim
                  FROM {$this->table} c
                  INNER JOIN excursio e ON c.id_excursio = e.id
                  INNER JOIN cim ON e.id_cim = cim.id
                  WHERE c.id_usuari = :id_usuari
                  ORDER BY c.created_at DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir els comentaris de l'usuari: " . $e->getMessage();
            return [];
        }
    }

    /**
     * UPDATE - Actualitzar un comentari existent
     * @return bool True si s'ha actualitzat correctament, false si hi ha error
     */
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET contingut = :contingut
                  WHERE id = :id 
                  AND id_usuari = :id_usuari";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Netegem el contingut
            $contingut_net = htmlspecialchars(strip_tags($this->contingut));
            
            // Vincular paràmetres
            $stmt->bindParam(':contingut', $contingut_net);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al actualitzar el comentari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE - Eliminar un comentari
     * @param int $id ID del comentari a eliminar
     * @param int $id_usuari ID de l'usuari (per verificar que és el propietari)
     * @return bool True si s'ha eliminat correctament, false si hi ha error
     */
    public function delete($id, $id_usuari) {
        // Només pot eliminar el propietari del comentari
        $query = "DELETE FROM {$this->table} 
                  WHERE id = :id 
                  AND id_usuari = :id_usuari";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar el comentari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE (ADMIN) - Eliminar un comentari (només administrador)
     * @param int $id ID del comentari a eliminar
     * @return bool True si s'ha eliminat correctament, false si hi ha error
     */
    public function deleteByAdmin($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar el comentari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * COUNT - Comptar el número de comentaris d'una excursió
     * @param int $id_excursio ID de l'excursió
     * @return int Número de comentaris
     */
    public function countByExcursio($id_excursio) {
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE id_excursio = :id_excursio";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_excursio', $id_excursio, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
            
        } catch(PDOException $e) {
            echo "Error al comptar els comentaris: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * VALIDAR - Validar que el contingut del comentari és correcte
     * @param string $contingut Text del comentari
     * @return array Array amb 'valid' (bool) i 'error' (string si hi ha error)
     */
    public static function validate($contingut) {
        $errors = [];

        // Comprovar que no estigui buit
        if (empty(trim($contingut))) {
            $errors[] = "El comentari no pot estar buit";
        }

        // Comprovar longitud mínima
        if (strlen(trim($contingut)) < 3) {
            $errors[] = "El comentari ha de tenir almenys 3 caràcters";
        }

        // Comprovar longitud màxima (200 caràcters segons la BD)
        if (strlen($contingut) > 200) {
            $errors[] = "El comentari no pot superar els 200 caràcters";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
?>