<?php
session_start();

// 1) Validación mínima
$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';
if ($identifier === '' || $password === '') {
  header('Location: /login.php?e=missing'); exit;
}

// 2) Conexión PDO
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

// 3) Buscar por nombre de usuario o mail
$sql = "SELECT id, nom_usuari, nom, cognom, foto, contrasenya, rol
        FROM usuari
        WHERE nom_usuari = ? OR mail = ?
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$identifier, $identifier]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 4) Verificar contraseña
if (!$user || !password_verify($password, $user['contrasenya'])) {
  header('Location: /login.php?e=invalid'); exit;
}

// 5) Seguridad sesión + guardar datos
session_regenerate_id(true); // <— añadido: mitiga fijación de sesión
$_SESSION['user_id']  = (int)$user['id'];
$_SESSION['username'] = $user['nom_usuari'];
$_SESSION['rol']      = $user['rol']; // 'usuari' o 'administrador'
$_SESSION['nom']    = $user['nom'];
$_SESSION['cognom'] = $user['cognom'];
$_SESSION['foto']   = $user['foto'];


header('Location: /perfil.php');
exit;
