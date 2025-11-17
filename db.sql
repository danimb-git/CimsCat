-- Crear base de dades
CREATE DATABASE IF NOT EXISTS cimscat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cimscat;

-- Taula Usuari
CREATE TABLE IF NOT EXISTS usuari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_usuari VARCHAR(25) NOT NULL UNIQUE,
    nom VARCHAR(25) NOT NULL,
    cognom VARCHAR(50) NOT NULL,
    mail VARCHAR(75) NOT NULL UNIQUE,
    contrasenya VARCHAR(255) NOT NULL,
    edat INT,
    rol ENUM('usuari', 'administrador') DEFAULT 'usuari',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula Cim
CREATE TABLE IF NOT EXISTS cim (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    alcada INT NOT NULL,
    comarca VARCHAR(70) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula Excursio
CREATE TABLE IF NOT EXISTS excursio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titol VARCHAR(100) NOT NULL,
    descripcio VARCHAR(300),
    data DATE NOT NULL,
    temps_ruta VARCHAR(25),
    dificultat ENUM('facil', 'mig', 'dificil') NOT NULL,
    imatges VARCHAR(255),
    distancia INT,
    id_cim INT NOT NULL,
    id_usuari INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cim) REFERENCES cim(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuari) REFERENCES usuari(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula Comentari
CREATE TABLE IF NOT EXISTS comentari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contingut VARCHAR(200) NOT NULL,
    data DATE NOT NULL,
    id_excursio INT NOT NULL,
    id_usuari INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_excursio) REFERENCES excursio(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuari) REFERENCES usuari(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula Like
CREATE TABLE IF NOT EXISTS `like` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    id_excursio INT NOT NULL,
    id_usuari INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (id_excursio, id_usuari),
    FOREIGN KEY (id_excursio) REFERENCES excursio(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuari) REFERENCES usuari(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CREEM UN PERFIL D'ADMINISTRADOR
INSERT INTO usuari (nom_usuari, nom, cognom, mail, contrasenya, edat, rol) VALUES
('admin', 'Admin', 'Sistema', 'admin@cimscat.cat', '12345678', 30, 'administrador');


ALTER TABLE usuari ADD COLUMN foto VARCHAR(255) NULL AFTER cognom;
UPDATE usuari SET foto = 'uploads/avatars/default.png' WHERE foto IS NULL;

ALTER TABLE excursio
  ADD COLUMN cim_nom     VARCHAR(200) AFTER distancia,
  ADD COLUMN cim_alcada  INT          AFTER cim_nom,
  ADD COLUMN cim_comarca VARCHAR(70)  AFTER cim_alcada;

ALTER TABLE excursio DROP FOREIGN KEY excursio_ibfk_1;

ALTER TABLE excursio MODIFY id_cim INT NULL;

UPDATE excursio e
JOIN cim c ON e.id_cim = c.id
SET e.cim_nom = c.nom, e.cim_alcada = c.alcada, e.cim_comarca = c.comarca
WHERE e.id_cim IS NOT NULL;