<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$sql = "INSERT INTO usuaris (nom_usuari, nom, cognom, mail, contrasenya_hash, rol)
        VALUES (:u, :n, :c, :m, :h, :r)";
$st = $pdo->prepare($sql);
$st->execute([
  ':u' => $_POST['nom_usuari'] ?? '',
  ':n' => $_POST['nom']        ?? '',
  ':c' => $_POST['cognom']     ?? '',
  ':m' => $_POST['mail']       ?? '',
  ':h' => password_hash($_POST['contrasenya'] ?? '1234', PASSWORD_DEFAULT),
  ':r' => $_POST['rol']        ?? 'usuari',
]);
echo "ID nou usuari: " . $pdo->lastInsertId();
