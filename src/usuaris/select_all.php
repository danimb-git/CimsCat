<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$st = $pdo->query("SELECT id, nom_usuari, nom, cognom, mail, rol, creat_el FROM usuaris ORDER BY creat_el DESC");
header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetchAll());
