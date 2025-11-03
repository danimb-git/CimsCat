<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
try {
  $st = $pdo->prepare("INSERT INTO likes (excursio_id, usuari_id) VALUES (:e,:u)");
  $st->execute([':e'=>(int)($_POST['excursio_id']??0), ':u'=>(int)($_POST['usuari_id']??0)]);
  echo "Like fet (#{$pdo->lastInsertId()})";
} catch (PDOException $e) {
  // per la clau única (usuari no pot fer dos likes a la mateixa excursió)
  echo "No s'ha pogut fer like: " . $e->getMessage();
}
