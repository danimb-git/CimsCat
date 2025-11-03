<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$e = (int)($_GET['excursio_id'] ?? 0);
$st = $pdo->prepare("SELECT COUNT(*) AS likes FROM likes WHERE excursio_id=:e");
$st->execute([':e'=>$e]);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetch());
