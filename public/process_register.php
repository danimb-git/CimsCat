<?php
// public/process_register.php
session_start();

// 1) Recoger y validar lo básico
$nom_usuari = trim($_POST['nom_usuari'] ?? '');
$nom        = trim($_POST['nom'] ?? '');
$cognom     = trim($_POST['cognom'] ?? '');
$mail       = trim($_POST['mail'] ?? '');
$password   = $_POST['password'] ?? '';
$password2  = $_POST['password2'] ?? '';
if ($password !== $password2) {
  header('Location: /register.php?e=pw_mismatch'); exit;
}
$edat       = trim($_POST['edat'] ?? '');
$rol        = 'usuari';

if ($nom_usuari === '' || $nom === '' || $cognom === '' || $mail === '' || $password === '') {
  header('Location: /register.php?e=missing'); exit;
}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
  header('Location: /register.php?e=badmail'); exit;
}

// 2) Conexión PDO
require_once __DIR__ . '/../src/config/Database.php';
$pdo = Database::getConnection();

// 3) Comprobar que nom_usuari o mail no existen (UNIQUE en BD)
$st = $pdo->prepare('SELECT id FROM usuari WHERE nom_usuari = ? OR mail = ? LIMIT 1');
$st->execute([$nom_usuari, $mail]);
if ($st->fetch()) {
  header('Location: /register.php?e=exists'); exit;
}

// 4) Hashear y guardar
$hash = password_hash($password, PASSWORD_DEFAULT);

$st = $pdo->prepare('INSERT INTO usuari (nom_usuari, nom, cognom, mail, contrasenya, edat, rol)
                     VALUES (?, ?, ?, ?, ?, ?, ?)');
$ok = $st->execute([
  $nom_usuari,
  $nom,
  $cognom,
  $mail,
  $hash,
  $edat !== '' ? (int)$edat : null,
  $rol
]);

if (!$ok) {
  header('Location: /register.php?e=dberr'); exit;
}

// 5) Éxito → al login
header('Location: /login.php?e=registered');
exit;
