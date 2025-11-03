<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$id = (int)($_POST['id'] ?? 0);
$st = $pdo->prepare("DELETE FROM excursions WHERE id=:id");
$st->execute([':id'=>$id]);
echo "Eliminades: " . $st->rowCount();
