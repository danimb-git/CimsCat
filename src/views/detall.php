<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Excursio.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$excursio = $id ? Excursio::getById($id) : null;
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Detall d'excursió</title>
  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
  <nav>
    <a href="/src/views/llistat.php">← Tornar al llistat</a>
  </nav>

  <?php if (!$excursio): ?>
    <h1>No s'ha trobat l'excursió</h1>
  <?php else: ?>
    <h1><?= htmlspecialchars($excursio['nom']) ?></h1>
    <ul>
      <li><b>Comarca:</b> <?= htmlspecialchars($excursio['comarca']) ?></li>
      <li><b>Alçada:</b> <?= htmlspecialchars($excursio['alcada']) ?> m</li>
      <li><b>Dificultat:</b> <?= htmlspecialchars($excursio['dificultat']) ?></li>
      <li><b>Temps ruta:</b> <?= htmlspecialchars($excursio['temps_ruta']) ?></li>
      <li><b>Distància:</b> <?= htmlspecialchars($excursio['distancia']) ?> km</li>
    </ul>
    <p><?= nl2br(htmlspecialchars($excursio['descripcio'])) ?></p>
  <?php endif; ?>
</body>
</html>
