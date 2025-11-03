<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$sql = "UPDATE usuaris
        SET nom_usuari=:u, nom=:n, cognom=:c, mail=:m, rol=:r
        WHERE id=:id";
$st = $pdo->prepare($sql);
$st->execute([
  ':u'=>$_POST['nom_usuari']??'',
  ':n'=>$_POST['nom']??'',
  ':c'=>$_POST['cognom']??'',
  ':m'=>$_POST['mail']??'',
  ':r'=>$_POST['rol']??'usuari',
  ':id'=>(int)($_POST['id']??0),
]);
echo "Actualitzades: " . $st->rowCount();
