<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Excursio.php';

// Obtén el terme de cerca (si n'hi ha)
$q = $_GET['q'] ?? null;

// Si hi ha cerca -> filtra; si no -> totes
$model = new Excursio();

if ($q) {
    $excursions = $model->search($q);
} else {
    $excursions = $model->getAll();
}


?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Llistat d'excursions</title>
  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
  <nav>
    <a href="/src/views/llistat.php">🏞️ Llistat</a>
    <a href="/src/views/detall.php?id=1">🔍 Exemple detall</a>
    <a href="/public/index.php">🏠 Inici</a>
  </nav>

  <h1>Llistat d'excursions</h1>

  <form method="get" action="llistat.php">
    <input type="text" name="q" placeholder="Cerca per nom o comarca"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    <button type="submit">Cercar</button>
  </form>

  <?php if (empty($excursions)): ?>
    <p>No s'ha trobat cap excursió.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Nom</th><th>Comarca</th><th>Alçada</th>
          <th>Dificultat</th><th>Temps ruta</th><th>Distància</th><th>Detall</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($excursions as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['nom']) ?></td>
            <td><?= htmlspecialchars($e['comarca']) ?></td>
            <td><?= htmlspecialchars($e['alcada']) ?> m</td>
            <td><?= htmlspecialchars($e['dificultat']) ?></td>
            <td><?= htmlspecialchars($e['temps_ruta']) ?></td>
            <td><?= htmlspecialchars($e['distancia']) ?> km</td>
            <td><a href="detall.php?id=<?= $e['id'] ?>">Veure més</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
