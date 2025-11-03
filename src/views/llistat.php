<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Llistat de cims</title>
</head>
<body>
  <h1>🗻 Llistat de cims</h1>

  <form method="GET" action="index.php">
    <input type="hidden" name="action" value="cercar">
    <input type="text" name="q" placeholder="Cerca per nom o comarca">
    <button type="submit">Cercar</button>
  </form>

  <ul>
    <?php foreach ($cims as $cim): ?>
      <li>
        <a href="index.php?action=veure&id=<?= $cim['id'] ?>">
          <?= htmlspecialchars($cim['nom']) ?> (<?= htmlspecialchars($cim['comarca']) ?>)
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>
