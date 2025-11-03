<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$e = (int)($_GET['excursio_id'] ?? 0);
$sql = "SELECT c.*, u.nom_usuari
        FROM comentaris c
        JOIN usuaris u ON u.id=c.usuari_id
        WHERE c.excursio_id=:e
        ORDER BY c.creat_el ASC";
$st = $pdo->prepare($sql);
$st->execute([':e'=>$e]);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetchAll());
