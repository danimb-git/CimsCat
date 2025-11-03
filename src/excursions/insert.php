<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$sql = "INSERT INTO excursions (titol, descripcio, data_excursio, comarca, dificultat, creat_per)
        VALUES (:titol, :descripcio, :data_excursio, :comarca, :dificultat, :creat_per)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':titol'        => $_POST['titol']        ?? 'Sense títol',
  ':descripcio'   => $_POST['descripcio']   ?? null,
  ':data_excursio'=> $_POST['data_excursio']?? null, // YYYY-MM-DD
  ':comarca'      => $_POST['comarca']      ?? null,
  ':dificultat'   => $_POST['dificultat']   ?? 'mig',
  ':creat_per'    => $_POST['creat_per']    ? (int)$_POST['creat_per'] : null,
]);
echo "ID nova excursió: " . $pdo->lastInsertId();
