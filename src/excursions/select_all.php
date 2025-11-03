<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$q = $_GET['q'] ?? null;
if ($q) {
  $sql = "SELECT e.*, u.nom_usuari AS autor
          FROM excursions e
          LEFT JOIN usuaris u ON u.id=e.creat_per
          WHERE e.titol LIKE :q OR e.comarca LIKE :q
          ORDER BY e.creat_el DESC";
  $st = $pdo->prepare($sql);
  $st->execute([':q'=>"%$q%"]);
} else {
  $st = $pdo->query("SELECT e.*, u.nom_usuari AS autor
                     FROM excursions e
                     LEFT JOIN usuaris u ON u.id=e.creat_per
                     ORDER BY e.creat_el DESC");
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($st->fetchAll());
