<?php
session_start();
require __DIR__ . '/../src/config/Database.php';
require __DIR__ . '/../src/models/Usuari.php';

function redirect($to){ header("Location: $to"); exit; }

// 1) Recollir inputs
$nom_usuari = trim($_POST['nom_usuari'] ?? '');
$nom        = trim($_POST['nom'] ?? '');
$cognom     = trim($_POST['cognom'] ?? '');
$mail       = trim($_POST['mail'] ?? '');
$pass       = $_POST['contrasenya'] ?? '';
$pass2      = $_POST['contrasenya2'] ?? '';

// 2) Validació molt bàsica
if ($nom_usuari==='' || $nom==='' || $cognom==='' || !filter_var($mail, FILTER_VALIDATE_EMAIL) || strlen($pass)<8 || $pass!==$pass2) {
  redirect('/register.php?e=validacio');
}


// 3) Insert usuari
try {
  $pdo = (new Database())->getConnection();
  // comprovar si ja existeix el correu
  $st = $pdo->prepare("SELECT 1 FROM usuari WHERE mail=?");
  $st->execute([$mail]);
  if ($st->fetchColumn()) redirect('/register.php?e=existeix');

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $st = $pdo->prepare("INSERT INTO usuari (nom_usuari,nom,cognom,mail,contrasenya) VALUES (?,?,?,?,?)");
  $st->execute([$nom_usuari,$nom,$cognom,$mail,$hash]);

  $_SESSION['user_id'] = (int)$pdo->lastInsertId();
  redirect('/perfil.html?s=ok');
} catch (Throwable $e) {
  redirect('/register.php?e=bd');
}