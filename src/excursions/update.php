<?php
include __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$sql = "UPDATE excursions
        SET titol=:titol, descripcio=:descripcio, data_excursio=:data_excursio,
            comarca=:comarca, dificultat=:dificultat
        WHERE id=:id";
$st = $pdo->prepare($sql);
$st->execute([
  ':titol'        => $_POST['titol']        ?? '',
  ':descripcio'   => $_POST['descripcio']   ?? null,
  ':data_excursio'=> $_POST['data_excursio']?? null,
  ':comarca'      => $_POST['comarca']      ?? null,
  ':dificultat'   => $_POST['dificultat']   ?? 'mig',
  ':id'           => (int)($_POST['id']     ?? 0),
]);
echo "Actualitzades: " . $st->rowCount();
