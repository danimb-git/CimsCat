<?php
/**
 * Executa-ho una sola vegada des del navegador o CLI:
 * 1) Navegador: http://localhost:8088/setup/create_db_and_tables.php
 * 2) CLI: php setup/create_db_and_tables.php
 */
$host    = '127.0.0.1';
$port    = '3306';
$user    = 'daliajordan';
$pass    = '123456'; 
$charset = 'utf8mb4';
$dbname  = 'cimscat';

try {
    // 1) Connecta sense DB per poder crear-la
    $pdo0 = new PDO("mysql:host=$host;port=$port;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 2) Crea la base de dades si no existeix
    $pdo0->exec("
        CREATE DATABASE IF NOT EXISTS `$dbname`
        DEFAULT CHARACTER SET $charset
        COLLATE utf8mb4_general_ci
    ");

    // 3) Reconnecta JA a la BD
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 4) Crea taules
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuaris (
          id INT AUTO_INCREMENT PRIMARY KEY,
          nom_usuari VARCHAR(50) NOT NULL UNIQUE,
          nom VARCHAR(100) NOT NULL,
          cognom VARCHAR(100) NOT NULL,
          mail VARCHAR(120) NOT NULL UNIQUE,
          contrasenya_hash VARCHAR(255) NOT NULL,
          rol ENUM('usuari','administrador') DEFAULT 'usuari',
          creat_el TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS excursions (
          id INT AUTO_INCREMENT PRIMARY KEY,
          titol VARCHAR(150) NOT NULL,
          descripcio TEXT,
          data_excursio DATE,
          comarca VARCHAR(120),
          dificultat ENUM('facil','mig','dificil') DEFAULT 'mig',
          creat_per INT NULL,
          creat_el TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (creat_per) REFERENCES usuaris(id)
            ON DELETE SET NULL ON UPDATE CASCADE
        );

        CREATE TABLE IF NOT EXISTS comentaris (
          id INT AUTO_INCREMENT PRIMARY KEY,
          excursio_id INT NOT NULL,
          usuari_id   INT NOT NULL,
          text TEXT NOT NULL,
          creat_el TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (excursio_id) REFERENCES excursions(id)
            ON DELETE CASCADE ON UPDATE CASCADE,
          FOREIGN KEY (usuari_id) REFERENCES usuaris(id)
            ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE IF NOT EXISTS likes (
          id INT AUTO_INCREMENT PRIMARY KEY,
          excursio_id INT NOT NULL,
          usuari_id   INT NOT NULL,
          creat_el TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY uk_like (excursio_id, usuari_id),
          FOREIGN KEY (excursio_id) REFERENCES excursions(id)
            ON DELETE CASCADE ON UPDATE CASCADE,
          FOREIGN KEY (usuari_id) REFERENCES usuaris(id)
            ON DELETE CASCADE ON UPDATE CASCADE
        );
    ");

    echo "BD i taules creades OK.";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage();
}
