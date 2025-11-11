<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe Like
 * Model per gestionar els m'agrada (likes) de les excursions
 */
class Like {
    // Connexió a la base de dades i nom de la taula
    private $conn;
    private $table = '`like`';  // Cometes inverses perquè 'like' és paraula reservada SQL

    // Propietats de l'objecte Like
    public $id;
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
     * AFEGIR LIKE - Afegir un m'agrada a una excursió
     * @return bool True si s'ha afegit correctament, false si hi ha error
     */
    public function add() {
        // Primer comprovar si ja existeix el like
        if ($this->exists()) {
            return false; // Ja existeix, no podem afegir-lo de nou
        }

        $query = "INSERT INTO {$this->table} 
                  (data, id_excursio, id_usuari) 
                  VALUES 
                  (CURDATE(), :id_excursio, :id_usuari)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular paràmetres
            $stmt->bindParam(':id_excursio', $this->id_excursio, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
            
        } catch(PDOException $e) {
            echo "Error al afegir el like: " . $e->getMessage();
            return false;
        }
    }

    /**
     * ELIMINAR LIKE - Eliminar un m'agrada d'una excursió
     * @return bool True si s'ha eliminat correctament, false si hi ha error
     */
    public function remove() {
        $query = "DELETE FROM {$this->table} 
                  WHERE id_excursio = :id_excursio 
                  AND id_usuari = :id_usuari";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_excursio', $this->id_excursio, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar el like: " . $e->getMessage();
            return false;
        }
    }

    /**
     * COMPROVAR EXISTÈNCIA - Comprovar si un usuari ja ha fet like a una excursió
     * @return bool True si existeix, false si no existeix
     */
    public function exists() {
        $query = "SELECT id FROM {$this->table} 
                  WHERE id_excursio = :id_excursio 
                  AND id_usuari = :id_usuari 
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_excursio', $this->id_excursio, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch(PDOException $e) {
            echo "Error al comprovar el like: " . $e->getMessage();
            return false;
        }
    }

    /**
     * COMPTAR LIKES - Comptar el número de likes d'una excursió
     * @param int $id_excursio ID de l'excursió
     * @return int Número de likes
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
            echo "Error al comptar els likes: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * OBTENIR LIKES D'USUARI - Obtenir totes les excursions que li agraden a un usuari
     * @param int $id_usuari ID de l'usuari
     * @return array Array amb les excursions que li agraden
     */
    public function getByUsuari($id_usuari) {
        $query = "SELECT l.*, e.titol, e.imatges, e.dificultat, c.nom as nom_cim
                  FROM {$this->table} l
                  INNER JOIN excursio e ON l.id_excursio = e.id
                  INNER JOIN cim c ON e.id_cim = c.id
                  WHERE l.id_usuari = :id_usuari
                  ORDER BY l.created_at DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir els likes de l'usuari: " . $e->getMessage();
            return [];
        }
    }

    /**
     * TOGGLE LIKE - Afegir o eliminar like segons si existeix o no
     * @return array Retorna array amb 'success', 'action' (added/removed) i 'count'
     */
    public function toggle() {
        if ($this->exists()) {
            // Si existeix, l'eliminem
            $success = $this->remove();
            $action = 'removed';
        } else {
            // Si no existeix, l'afegim
            $success = $this->add();
            $action = 'added';
        }

        // Comptar els likes actuals
        $count = $this->countByExcursio($this->id_excursio);

        return [
            'success' => $success,
            'action' => $action,
            'count' => $count,
            'liked' => $action === 'added'
        ];
    }
}
?>