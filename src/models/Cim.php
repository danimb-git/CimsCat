<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe Cim
 * Model per gestionar les operacions CRUD de la taula cim
 */
class Cim {
    // Connexió a la base de dades i nom de la taula
    private $conn;
    private $table = 'cim';

    // Propietats de l'objecte Cim
    public $id;
    public $nom;
    public $alcada;
    public $comarca;
    public $created_at;

    /**
     * Constructor: inicialitza la connexió
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * CREATE - Crear un nou cim
     * @return bool True si s'ha creat correctament, false si hi ha error
     */
    public function create() {
        $query = "INSERT INTO {$this->table} (nom, alcada, comarca) VALUES (:nom, :alcada, :comarca)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular paràmetres
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':alcada', $this->alcada, PDO::PARAM_INT);
            $stmt->bindParam(':comarca', $this->comarca);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
            
        } catch(PDOException $e) {
            echo "Error al crear el cim: " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ - Obtenir tots els cims
     * @return array Array amb tots els cims
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY alcada DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir els cims: " . $e->getMessage();
            return [];
        }
    }

    /**
     * READ - Obtenir un cim per ID
     * @param int $id ID del cim
     * @return array|false Array amb les dades del cim o false si no existeix
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                $this->id = $result['id'];
                $this->nom = $result['nom'];
                $this->alcada = $result['alcada'];
                $this->comarca = $result['comarca'];
                $this->created_at = $result['created_at'];
            }
            
            return $result;
            
        } catch(PDOException $e) {
            echo "Error al obtenir el cim: " . $e->getMessage();
            return false;
        }
    }

    /**
     * UPDATE - Actualitzar un cim existent
     * @return bool True si s'ha actualitzat correctament, false si hi ha error
     */
    public function update() {
        $query = "UPDATE {$this->table} SET nom = :nom, alcada = :alcada, comarca = :comarca WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular paràmetres
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':alcada', $this->alcada, PDO::PARAM_INT);
            $stmt->bindParam(':comarca', $this->comarca);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al actualitzar el cim: " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE - Eliminar un cim
     * @param int $id ID del cim a eliminar
     * @return bool True si s'ha eliminat correctament, false si hi ha error
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar el cim: " . $e->getMessage();
            return false;
        }
    }

    /**
     * SEARCH - Cercar cims per nom o comarca
     * @param string $search Text de cerca
     * @return array Array amb els cims trobats
     */
    public function search($search) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE nom LIKE :search OR comarca LIKE :search 
                  ORDER BY alcada DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al cercar cims: " . $e->getMessage();
            return [];
        }
    }
}
?>