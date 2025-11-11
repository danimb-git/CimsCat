<?php
// process_eliminar_excursio.php
session_start();

// 0) Només acceptem POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  header('Location: /perfiladministrador.php?e=method'); exit;
}

// 1) Comprovar sessió i rol
if (empty($_SESSION['user_id'])) { header('Location: /login.php?e=required'); exit; }
if (($_SESSION['rol'] ?? '') !== 'administrador') {
  header('Location: /perfil.php'); exit;
}

// 2) Validar ID rebut
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
  header('Location: /perfiladministrador.php?e=id'); exit;
}

// 3) Connexió PDO
// IMPORTANT: ajusta la ruta segons on guardis aquest fitxer.
// Si AQUEST fitxer és a l'arrel (/) -> usa 'src/config/Database.php'
// Si és a /public o /pages -> potser necessites '../src/config/Database.php'
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

try {
  // 4) Esborrar (DELETE segur i acotat a 1 fila)
  $stmt = $pdo->prepare('DELETE FROM excursio WHERE id = ? LIMIT 1');
  $stmt->execute([$id]);

  if ($stmt->rowCount() === 1) {
    header('Location: /perfiladministrador.php?ok=deleted');
  } else {
    // No existia o ja no hi era
    header('Location: /perfiladministrador.php?e=notfound');
  }
  exit;

} catch (PDOException $ex) {
  // Possibles conflictes de FK si tens comentaris/likes sense ON DELETE CASCADE
  // Pots loguejar $ex->getMessage() i retornar un codi d'error més clar:
  header('Location: /perfiladministrador.php?e=db');
  exit;
}
