-- ========================================
-- SCRIPT DE DADES DE PROVA - CIMSCAT
-- Versió: SENSE DUPLICATS
-- Data: 11 novembre 2025
-- ========================================

USE cimscat;

-- ========================================
-- 1. USUARIS (només si no existeixen)
-- ========================================

INSERT IGNORE INTO usuari (id, nom_usuari, nom, cognom, mail, contrasenya, edat, rol) VALUES
(1, 'admin', 'Admin', 'Sistema', 'admin@cimscat.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 30, 'administrador'),
(2, 'joan_garcia', 'Joan', 'Garcia', 'joan@test.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 25, 'usuari'),
(3, 'maria_lopez', 'Maria', 'Lopez', 'maria@test.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 30, 'usuari'),
(4, 'pere_sanchez', 'Pere', 'Sanchez', 'pere@test.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 28, 'usuari'),
(5, 'anna_martinez', 'Anna', 'Martinez', 'anna@test.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 26, 'usuari'),
(6, 'david_fernandez', 'David', 'Fernandez', 'david@test.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 32, 'usuari');

-- ========================================
-- 2. CIMS (només si no existeixen)
-- ========================================

INSERT IGNORE INTO cim (id, nom, alcada, comarca) VALUES
(1, 'Montserrat', 1236, 'Bages'),
(2, 'Cadiretes', 532, 'Baix Empordà'),
(3, 'Puigmal', 2913, 'Ripollès'),
(4, 'Pedraforca', 2506, 'Berguedà'),
(5, 'Canigó', 2784, 'Conflent'),
(6, 'Montseny', 1712, 'Vallès Oriental'),
(7, 'Tibidabo', 512, 'Barcelonès'),
(8, 'Montcau', 1056, 'Bages'),
(9, 'Cavall Bernat', 1111, 'Bages'),
(10, 'Taga', 2040, 'Cerdanya');

-- ========================================
-- 3. EXCURSIONS (només si no existeixen)
-- ========================================

INSERT IGNORE INTO excursio (id, titol, descripcio, data, temps_ruta, dificultat, imatges, distancia, id_cim, id_usuari) VALUES
(1, 'Excursió a Montserrat pel Camí de Sant Jeroni', 'Descobreix els millors camins per gaudir de la muntanya de Montserrat. Una ruta espectacular amb vistes impressionants del Monestir i les formacions rocoses úniques. Ideal per a famílies i aficionats a la muntanya.', '2025-01-15', '3 hores', 'mig', 'montserrat.jpg', 12, 1, 2),
(2, 'Ruta Circular per les Cadiretes', 'Descobreix els millors camins per gaudir de la muntanya de Cadiretes. Perfecta per a principiants, amb vistes al mar Mediterrani i paisatges de bosc mediterrani. Ruta familiar amb zones de pícnic.', '2025-02-10', '2 hores', 'facil', 'cadiretes.jpg', 8, 2, 2),
(3, 'Ascensió al Puigmal des de Vallter', 'Descobreix els millors camins per gaudir de la muntanya de Puigmal. Ruta exigent per a excursionistes experimentats amb vistes panoràmiques dels Pirineus. Equipament de muntanya necessari.', '2025-03-05', '6 hores', 'dificil', 'puigmal.jpg', 18, 3, 3),
(4, 'La Clàssica del Pedraforca', 'Ascens a una de les muntanyes més emblemàtiques de Catalunya. Ruta circular amb vistes espectaculars i pas pel coll del Verdet. Recomanable experiència prèvia en muntanya.', '2025-03-20', '7 hores', 'dificil', 'placeholder.jpg', 16, 4, 4),
(5, 'Pujada al Canigó per Mariailles', 'Conquesta del cim més meridional dels Pirineus. Ruta llarga i exigent amb desnivell important. Vistes increïbles de la costa catalana i francesa. Sortida matinera recomanada.', '2025-04-10', '8 hores', 'dificil', 'placeholder.jpg', 20, 5, 5),
(6, 'Turó de l\'Home - El Cim del Montseny', 'Ruta al punt més alt del Montseny. Paisatge de fagedes i alzinars. Dificultat moderada amb bon camí senyalitzat. Ideal per a un cap de setmana a prop de Barcelona.', '2025-04-25', '4 hores', 'mig', 'placeholder.jpg', 14, 6, 2),
(7, 'Passejada al Tibidabo', 'Ruta urbana fins al cim del Tibidabo amb visites al parc d\'atraccions i el temple del Sagrat Cor. Perfecta per a tota la família. Accessible amb transport públic.', '2025-05-05', '1.5 hores', 'facil', 'placeholder.jpg', 5, 7, 3),
(8, 'Circular de Montcau', 'Ruta circular pel massís de Montcau amb vistes del Pla de Bages. Camí ben senyalitzat amb zones d\'ombra. Dificultat moderada, recomanable a primavera.', '2025-05-20', '3.5 hores', 'mig', 'placeholder.jpg', 11, 8, 4),
(9, 'Pujada al Cavall Bernat', 'Ascens a l\'emblemàtica agulla de Montserrat. Ruta curta però amb trams amb cadenes. Vistes espectaculars de tot el massís. Precaució amb vèrtig.', '2025-06-01', '2.5 hores', 'mig', 'placeholder.jpg', 6, 9, 5),
(10, 'Cim de Taga - La Muntanya Màgica', 'Ruta fins al cim de Taga, conegut per les seves llegendes. Paisatge pirenaico amb prats alpins. Dificultat mitjana-alta. Millor època: primavera i estiu.', '2025-06-15', '5 hores', 'mig', 'placeholder.jpg', 15, 10, 6);

-- ========================================
-- 4. COMENTARIS (només si no existeixen)
-- ========================================

INSERT IGNORE INTO comentari (id, contingut, data, id_excursio, id_usuari) VALUES
-- Excursió 1 (Montserrat)
(1, 'Molt bonica aquesta ruta! Les vistes són espectaculars des de dalt.', '2025-01-16', 1, 3),
(2, 'Hi vaig anar el cap de setmana passat. Recomanable 100%!', '2025-01-18', 1, 4),
(3, 'Perfecta per anar amb la família. Els nens ho van gaudir molt.', '2025-01-20', 1, 5),
(4, 'El camí està molt ben senyalitzat. Ens va costar 3h tal com diu.', '2025-01-22', 1, 6),

-- Excursió 2 (Cadiretes)
(5, 'Ideal per començar a fer muntanya. No és massa difícil.', '2025-02-11', 2, 2),
(6, 'Les vistes al mar són precioses! Hi tornarem segur.', '2025-02-13', 2, 4),
(7, 'Vam fer un pícnic a dalt i va ser genial.', '2025-02-15', 2, 5),

-- Excursió 3 (Puigmal)
(8, 'Ruta dura però les vistes valen la pena. Porteu aigua!', '2025-03-06', 3, 2),
(9, 'Molt de desnivell. Cal estar preparat físicament.', '2025-03-08', 3, 4),
(10, 'El millor cim dels Pirineus que he fet fins ara.', '2025-03-10', 3, 6),

-- Excursió 4 (Pedraforca)
(11, 'Increïble! El Pedraforca no decepciona mai.', '2025-03-21', 4, 2),
(12, 'El pas pel coll del Verdet és espectacular.', '2025-03-23', 4, 3),

-- Excursió 5 (Canigó)
(13, 'Una de les millors rutes que he fet. El Canigó és màgic!', '2025-04-11', 5, 2),
(14, 'Molt llarga però preciosa. Sortiu d\'hora!', '2025-04-13', 5, 4),

-- Excursió 6 (Montseny)
(15, 'El Montseny sempre és una bona opció. Bosc preciós.', '2025-04-26', 6, 3),
(16, 'Vam veure esquirols! Els nens van al·lucinar.', '2025-04-28', 6, 5),

-- Excursió 7 (Tibidabo)
(17, 'Perfecte per fer amb nens petits. També hi ha el parc!', '2025-05-06', 7, 2),
(18, 'Les vistes de Barcelona són increïbles des de dalt.', '2025-05-08', 7, 6),

-- Excursió 8 (Montcau)
(19, 'Ruta molt maca i assequible. Recomanable!', '2025-05-21', 8, 3),

-- Excursió 9 (Cavall Bernat)
(20, 'Les cadenes fan una mica de respecte però val la pena!', '2025-06-02', 9, 4),
(21, 'Vistes brutals de Montserrat. Imprescindible!', '2025-06-04', 9, 2),

-- Excursió 10 (Taga)
(22, 'La muntanya màgica de veritat! Energia especial aquí dalt.', '2025-06-16', 10, 5),
(23, 'Els prats florits a la primavera són una passada.', '2025-06-18', 10, 3);

-- ========================================
-- 5. LIKES (només si no existeixen)
-- ========================================

INSERT IGNORE INTO `like` (id, data, id_excursio, id_usuari) VALUES
-- Excursió 1 (Montserrat) - 5 likes
(1, '2025-01-16', 1, 2),
(2, '2025-01-17', 1, 3),
(3, '2025-01-18', 1, 4),
(4, '2025-01-19', 1, 5),
(5, '2025-01-20', 1, 6),

-- Excursió 2 (Cadiretes) - 4 likes
(6, '2025-02-11', 2, 2),
(7, '2025-02-12', 2, 3),
(8, '2025-02-13', 2, 5),
(9, '2025-02-14', 2, 6),

-- Excursió 3 (Puigmal) - 4 likes
(10, '2025-03-06', 3, 2),
(11, '2025-03-07', 3, 4),
(12, '2025-03-08', 3, 5),
(13, '2025-03-09', 3, 6),

-- Excursió 4 (Pedraforca) - 3 likes
(14, '2025-03-21', 4, 2),
(15, '2025-03-22', 4, 3),
(16, '2025-03-23', 4, 5),

-- Excursió 5 (Canigó) - 3 likes
(17, '2025-04-11', 5, 2),
(18, '2025-04-12', 5, 4),
(19, '2025-04-13', 5, 6),

-- Excursió 6 (Montseny) - 4 likes
(20, '2025-04-26', 6, 2),
(21, '2025-04-27', 6, 3),
(22, '2025-04-28', 6, 4),
(23, '2025-04-29', 6, 5),

-- Excursió 7 (Tibidabo) - 2 likes
(24, '2025-05-06', 7, 3),
(25, '2025-05-07', 7, 6),

-- Excursió 8 (Montcau) - 2 likes
(26, '2025-05-21', 8, 2),
(27, '2025-05-22', 8, 4),

-- Excursió 9 (Cavall Bernat) - 3 likes
(28, '2025-06-02', 9, 2),
(29, '2025-06-03', 9, 4),
(30, '2025-06-04', 9, 5),

-- Excursió 10 (Taga) - 2 likes
(31, '2025-06-16', 10, 3),
(32, '2025-06-17', 10, 5);

-- ========================================
-- CONSULTES DE VERIFICACIÓ
-- ========================================

SELECT '========================================' as '';
SELECT 'RESUM DE DADES A LA BASE DE DADES' as '';
SELECT '========================================' as '';

SELECT 'USUARIS:' as '';
SELECT COUNT(*) as total FROM usuari;

SELECT 'CIMS:' as '';
SELECT COUNT(*) as total FROM cim;

SELECT 'EXCURSIONS:' as '';
SELECT COUNT(*) as total FROM excursio;

SELECT 'COMENTARIS:' as '';
SELECT COUNT(*) as total FROM comentari;

SELECT 'LIKES:' as '';
SELECT COUNT(*) as total FROM `like`;

SELECT '========================================' as '';
SELECT '✅ Dades carregades correctament!' as '';
SELECT 'Contrasenya per tots els usuaris: 12345678' as '';
SELECT '========================================' as '';