<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$st = $pdo->prepare("DELETE FROM likes WHERE excursio_id=:e AND usuari_id=:u");
$st->execute([
  ':e'=>(int)($_POST['excursio_id']??0),
  ':u'=>(int)($_POST['usuari_id']??0),
]);
echo "Likes eliminats: " . $st->rowCount();
