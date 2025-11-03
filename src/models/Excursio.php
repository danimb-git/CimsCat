<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe Excursio
 * Model per gestionar les operacions CRUD de la taula excursio
 */
class Excursio {
    // Connexió a la base de dades i nom de la taula
    private $conn;
    private $table = 'excursio';

    // Propietats de l'objecte Excursio
    public $id;
    public $titol;
    public $descripcio;
    public $data;
    public $temps_ruta;
    public $dificultat;
    public $imatges;
    public $distancia;
    public $id_cim;
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
     * CREATE - Crear una nova excursió
     * @return bool True si s'ha creat correctament, false si hi ha error
     */
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (titol, descripcio, data, temps_ruta, dificultat, imatges, distancia, id_cim, id_usuari) 
                  VALUES 
                  (:titol, :descripcio, :data, :temps_ruta, :dificultat, :imatges, :distancia, :id_cim, :id_usuari)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular paràmetres
            $stmt->bindParam(':titol', $this->titol);
            $stmt->bindParam(':descripcio', $this->descripcio);
            $stmt->bindParam(':data', $this->data);
            $stmt->bindParam(':temps_ruta', $this->temps_ruta);
            $stmt->bindParam(':dificultat', $this->dificultat);
            $stmt->bindParam(':imatges', $this->imatges);
            $stmt->bindParam(':distancia', $this->distancia, PDO::PARAM_INT);
            $stmt->bindParam(':id_cim', $this->id_cim, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuari', $this->id_usuari, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
            
        } catch(PDOException $e) {
            echo "Error al crear l'excursió: " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ - Obtenir totes les excursions amb informació del cim i usuari
     * @return array Array amb totes les excursions
     */
    public function getAll() {
        $query = "SELECT e.*, c.nom as nom_cim, c.alcada, u.nom_usuari 
                  FROM {$this->table} e
                  INNER JOIN cim c ON e.id_cim = c.id
                  INNER JOIN usuari u ON e.id_usuari = u.id
                  ORDER BY e.data DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir les excursions: " . $e->getMessage();
            return [];
        }
    }

    /**
     * READ - Obtenir una excursió per ID amb informació relacionada
     * @param int $id ID de l'excursió
     * @return array|false Array amb les dades de l'excursió o false si no existeix
     */
    public function getById($id) {
        $query = "SELECT e.*, c.nom as nom_cim, c.alcada, c.comarca, 
                  u.nom_usuari, u.nom, u.cognom
                  FROM {$this->table} e
                  INNER JOIN cim c ON e.id_cim = c.id
                  INNER JOIN usuari u ON e.id_usuari = u.id
                  WHERE e.id = :id 
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                $this->id = $result['id'];
                $this->titol = $result['titol'];
                $this->descripcio = $result['descripcio'];
                $this->data = $result['data'];
                $this->temps_ruta = $result['temps_ruta'];
                $this->dificultat = $result['dificultat'];
                $this->imatges = $result['imatges'];
                $this->distancia = $result['distancia'];
                $this->id_cim = $result['id_cim'];
                $this->id_usuari = $result['id_usuari'];
                $this->created_at = $result['created_at'];
            }
            
            return $result;
            
        } catch(PDOException $e) {
            echo "Error al obtenir l'excursió: " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ - Obtenir excursions d'un usuari concret
     * @param int $id_usuari ID de l'usuari
     * @return array Array amb les excursions de l'usuari
     */
    public function getByUserId($id_usuari) {
        $query = "SELECT e.*, c.nom as nom_cim, c.alcada
                  FROM {$this->table} e
                  INNER JOIN cim c ON e.id_cim = c.id
                  WHERE e.id_usuari = :id_usuari
                  ORDER BY e.data DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al obtenir les excursions de l'usuari: " . $e->getMessage();
            return [];
        }
    }

    /**
     * UPDATE - Actualitzar una excursió existent
     * @return bool True si s'ha actualitzat correctament, false si hi ha error
     */
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET titol = :titol, 
                      descripcio = :descripcio, 
                      data = :data, 
                      temps_ruta = :temps_ruta, 
                      dificultat = :dificultat, 
                      imatges = :imatges, 
                      distancia = :distancia, 
                      id_cim = :id_cim 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular paràmetres
            $stmt->bindParam(':titol', $this->titol);
            $stmt->bindParam(':descripcio', $this->descripcio);
            $stmt->bindParam(':data', $this->data);
            $stmt->bindParam(':temps_ruta', $this->temps_ruta);
            $stmt->bindParam(':dificultat', $this->dificultat);
            $stmt->bindParam(':imatges', $this->imatges);
            $stmt->bindParam(':distancia', $this->distancia, PDO::PARAM_INT);
            $stmt->bindParam(':id_cim', $this->id_cim, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al actualitzar l'excursió: " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE - Eliminar una excursió
     * @param int $id ID de l'excursió a eliminar
     * @return bool True si s'ha eliminat correctament, false si hi ha error
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar l'excursió: " . $e->getMessage();
            return false;
        }
    }

    /**
     * SEARCH - Cercar excursions per títol, dificultat o cim
     * @param string $search Text de cerca
     * @return array Array amb les excursions trobades
     */
    public function search($search) {
        $query = "SELECT e.*, c.nom as nom_cim, c.alcada, u.nom_usuari
                  FROM {$this->table} e
                  INNER JOIN cim c ON e.id_cim = c.id
                  INNER JOIN usuari u ON e.id_usuari = u.id
                  WHERE e.titol LIKE :search 
                     OR e.dificultat LIKE :search 
                     OR c.nom LIKE :search
                  ORDER BY e.data DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al cercar excursions: " . $e->getMessage();
            return [];
        }
    }

    /**
     * FILTER - Filtrar excursions per dificultat
     * @param string $dificultat Dificultat (facil, mig, dificil)
     * @return array Array amb les excursions filtrades
     */
    public function filterByDificultat($dificultat) {
        $query = "SELECT e.*, c.nom as nom_cim, c.alcada, u.nom_usuari
                  FROM {$this->table} e
                  INNER JOIN cim c ON e.id_cim = c.id
                  INNER JOIN usuari u ON e.id_usuari = u.id
                  WHERE e.dificultat = :dificultat
                  ORDER BY e.data DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':dificultat', $dificultat);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            echo "Error al filtrar per dificultat: " . $e->getMessage();
            return [];
        }
    }
}
?>