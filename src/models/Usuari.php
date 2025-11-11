<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe Usuari
 * Model per gestionar les operacions relacionades amb usuaris
 */
class Usuari {
    private $conn;
    private $table = 'usuari'; // CORREGIT: abans era 'usuaris'

    // Propietats de l'objecte Usuari
    public $id;
    public $nom_usuari;
    public $nom;
    public $cognom;
    public $mail;
    public $contrasenya;
    public $edat;
    public $rol;
    public $created_at;

    /**
     * Constructor: inicialitza la connexió
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * REGISTRAR - Registrar un usuari nou
     * @param string $nom_usuari Nom d'usuari únic
     * @param string $nom Nom real
     * @param string $cognom Cognoms
     * @param string $mail Correu electrònic
     * @param string $contrasenya Contrasenya en text pla (es farà hash)
     * @param int $edat Edat de l'usuari
     * @return bool True si s'ha registrat correctament, false si hi ha error
     */
    public function register($nom_usuari, $nom, $cognom, $mail, $contrasenya, $edat = null) {
        // Hash de la contrasenya
        $hash = password_hash($contrasenya, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO {$this->table} 
                  (nom_usuari, nom, cognom, mail, contrasenya, edat, rol) 
                  VALUES 
                  (:nom_usuari, :nom, :cognom, :mail, :contrasenya, :edat, 'usuari')";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nom_usuari', $nom_usuari);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':cognom', $cognom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':contrasenya', $hash);
            $stmt->bindParam(':edat', $edat, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al registrar l'usuari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * LOGIN - Iniciar sessió
     * @param string $mail Correu electrònic
     * @param string $contrasenya Contrasenya
     * @return array|false Array amb les dades de l'usuari o false si no és correcte
     */
    public function login($mail, $contrasenya) {
        $query = "SELECT * FROM {$this->table} WHERE mail = :mail LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            
            $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contrasenya
            if ($usuari && password_verify($contrasenya, $usuari['contrasenya'])) {
                // No retornar la contrasenya
                unset($usuari['contrasenya']);
                return $usuari;
            }
            
            return false;
            
        } catch(PDOException $e) {
            echo "Error al fer login: " . $e->getMessage();
            return false;
        }
    }

    /**
     * GET BY ID - Obtenir dades d'un usuari per ID
     * @param int $id ID de l'usuari
     * @return array|false Array amb les dades de l'usuari (sense contrasenya)
     */
    public function getById($id) {
        $query = "SELECT id, nom_usuari, nom, cognom, mail, edat, rol, created_at 
                  FROM {$this->table} 
                  WHERE id = :id 
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $this->id = $result['id'];
                $this->nom_usuari = $result['nom_usuari'];
                $this->nom = $result['nom'];
                $this->cognom = $result['cognom'];
                $this->mail = $result['mail'];
                $this->edat = $result['edat'];
                $this->rol = $result['rol'];
                $this->created_at = $result['created_at'];
            }
            
            return $result;
            
        } catch(PDOException $e) {
            echo "Error al obtenir l'usuari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * GET ALL - Llistar tots els usuaris (només per administrador)
     * @return array Array amb tots els usuaris
     */
    public function getAll() {
        $query = "SELECT id, nom_usuari, nom, cognom, mail, edat, rol, created_at 
                  FROM {$this->table} 
                  ORDER BY created_at DESC";
        
        try {
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            echo "Error al obtenir els usuaris: " . $e->getMessage();
            return [];
        }
    }

    /**
     * UPDATE - Actualitzar dades d'un usuari
     * @return bool True si s'ha actualitzat correctament
     */
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET nom_usuari = :nom_usuari, 
                      nom = :nom, 
                      cognom = :cognom, 
                      mail = :mail, 
                      edat = :edat
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nom_usuari', $this->nom_usuari);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':cognom', $this->cognom);
            $stmt->bindParam(':mail', $this->mail);
            $stmt->bindParam(':edat', $this->edat, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al actualitzar l'usuari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * UPDATE PASSWORD - Canviar contrasenya
     * @param int $id ID de l'usuari
     * @param string $contrasenya_antiga Contrasenya actual
     * @param string $contrasenya_nova Nova contrasenya
     * @return bool True si s'ha canviat correctament
     */
    public function updatePassword($id, $contrasenya_antiga, $contrasenya_nova) {
        // Primer obtenir la contrasenya actual
        $query = "SELECT contrasenya FROM {$this->table} WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contrasenya antiga
            if (!$usuari || !password_verify($contrasenya_antiga, $usuari['contrasenya'])) {
                return false;
            }

            // Actualitzar amb la nova contrasenya
            $hash_nova = password_hash($contrasenya_nova, PASSWORD_DEFAULT);
            $query_update = "UPDATE {$this->table} SET contrasenya = :contrasenya WHERE id = :id";
            
            $stmt = $this->conn->prepare($query_update);
            $stmt->bindParam(':contrasenya', $hash_nova);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al canviar la contrasenya: " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE - Eliminar un usuari
     * @param int $id ID de l'usuari a eliminar
     * @return bool True si s'ha eliminat correctament
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            echo "Error al eliminar l'usuari: " . $e->getMessage();
            return false;
        }
    }

    /**
     * CHECK IF EXISTS - Comprovar si un mail o nom d'usuari ja existeixen
     * @param string $mail Correu electrònic
     * @param string $nom_usuari Nom d'usuari
     * @return array Array amb 'mail_exists' i 'username_exists'
     */
    public function checkIfExists($mail, $nom_usuari) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM {$this->table} WHERE mail = :mail) as mail_exists,
                    (SELECT COUNT(*) FROM {$this->table} WHERE nom_usuari = :nom_usuari) as username_exists";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':nom_usuari', $nom_usuari);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'mail_exists' => $result['mail_exists'] > 0,
                'username_exists' => $result['username_exists'] > 0
            ];
            
        } catch(PDOException $e) {
            echo "Error al comprovar l'existència: " . $e->getMessage();
            return [
                'mail_exists' => false,
                'username_exists' => false
            ];
        }
    }
}
?>