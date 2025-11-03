<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT id, nom_usuari, nom, cognom, mail, rol, creat_el FROM usuaris WHERE id=:id");
$st->execute([':id'=>$id]);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetch() ?: ['error'=>'No trobat']);
