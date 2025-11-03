<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($cim['nom']) ?></title>
</head>
<body>
  <h1><?= htmlspecialchars($cim['nom']) ?></h1>
  <p><strong>Comarca:</strong> <?= htmlspecialchars($cim['comarca']) ?></p>
  <p><strong>Alçada:</strong> <?= htmlspecialchars($cim['alcada']) ?> m</p>
  <p><strong>Descripció:</strong> <?= htmlspecialchars($cim['descripcio']) ?></p>

  <a href="index.php">← Tornar al llistat</a>
</body>
</html>
