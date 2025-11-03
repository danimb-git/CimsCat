<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT * FROM excursions WHERE id=:id");
$st->execute([':id'=>$id]);
$row = $st->fetch();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($row ?: ['error'=>'No trobat']);
