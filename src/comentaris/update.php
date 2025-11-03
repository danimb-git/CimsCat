<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$sql = "UPDATE comentaris SET text=:t WHERE id=:id";
$st = $pdo->prepare($sql);
$st->execute([':t'=>$_POST['text']??'', ':id'=>(int)($_POST['id']??0)]);
echo "Actualitzades: " . $st->rowCount();
