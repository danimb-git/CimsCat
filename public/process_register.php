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
$fotoPath = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
  ];

  $tmp  = $_FILES['foto']['tmp_name'];
  $mime = mime_content_type($tmp) ?: '';

  if (!isset($allowed[$mime])) {
    header('Location: /register.php?e=foto_tipo'); exit;
  }
  if ($_FILES['foto']['size'] > 2 * 1024 * 1024) { // 2MB
    header('Location: /register.php?e=foto_mida'); exit;
  }

  $ext = $allowed[$mime];
  $uploadDirFs = __DIR__ . '/uploads/avatars';
  if (!is_dir($uploadDirFs)) { mkdir($uploadDirFs, 0777, true); }

  $fileName = 'ava_' . bin2hex(random_bytes(8)) . '.' . $ext;
  $destFs   = $uploadDirFs . '/' . $fileName;

  if (!move_uploaded_file($tmp, $destFs)) {
    header('Location: /register.php?e=foto_move'); exit;
  }

  // IMPORTANT: desarem el path RELATIU per al src del <img>
  $fotoPath = 'uploads/avatars/' . $fileName;
}

// 2) Conexión PDO
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

// 3) Comprobar que nom_usuari o mail no existen (UNIQUE en BD)
$st = $pdo->prepare('SELECT id FROM usuari WHERE nom_usuari = ? OR mail = ? LIMIT 1');
$st->execute([$nom_usuari, $mail]);
if ($st->fetch()) {
  header('Location: /register.php?e=exists'); exit;
}

// 4) Hashear y guardar
$hash = password_hash($password, PASSWORD_DEFAULT);

$st = $pdo->prepare('INSERT INTO usuari (nom_usuari, nom, cognom, mail, contrasenya, edat, rol, foto)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$ok = $st->execute([
  $nom_usuari,
  $nom,
  $cognom,
  $mail,
  $hash,
  $edat !== '' ? (int)$edat : null,
  $rol,
  $fotoPath
]);

if (!$ok) {
  header('Location: /register.php?e=dberr'); exit;
}

// 5) Éxito → al login
header('Location: /login.php?e=registered');
exit;
