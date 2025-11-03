<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$sql = "INSERT INTO comentaris (excursio_id, usuari_id, text)
        VALUES (:e,:u,:t)";
$st = $pdo->prepare($sql);
$st->execute([
  ':e'=>(int)($_POST['excursio_id']??0),
  ':u'=>(int)($_POST['usuari_id']??0),
  ':t'=>$_POST['text']??'',
]);
echo "ID comentari: " . $pdo->lastInsertId();
