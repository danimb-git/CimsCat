<?php
/**
 * Classe Database
 * Gestiona la connexió a la base de dades utilitzant PDO
 */
class Database {
    // Propietats de connexió
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $conn;

    /**
     * Constructor: carrega les variables d'entorn del fitxer .env
     */
    public function __construct() {
        $this->loadEnv();
        $this->host = $_ENV['DB_HOST'] ?? 'db';
        $this->db_name = $_ENV['DB_NAME'] ?? 'cimscat';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? 'root';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }

    /**
     * Carrega les variables d'entorn des del fitxer .env
     */
    private function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Ignorar comentaris
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Separar clau=valor
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    /**
     * Estableix la connexió a la base de dades
     * @return PDO|null Retorna l'objecte PDO o null si hi ha error
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Crear DSN (Data Source Name)
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            // Opcions de PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Llançar excepcions en errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retornar arrays associatius
                PDO::ATTR_EMULATE_PREPARES => false // Desactivar emulació de prepared statements
            ];

            // Crear connexió PDO
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Error de connexió: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>