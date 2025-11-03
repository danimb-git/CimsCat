<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$st = $pdo->prepare("DELETE FROM usuaris WHERE id=:id");
$st->execute([':id'=>(int)($_POST['id']??0)]);
echo "Eliminades: " . $st->rowCount();
