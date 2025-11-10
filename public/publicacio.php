<?php
session_start();
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

/*
 Escapes a string for safe HTML output to prevent XSS vulnerabilities.
 @param string $s The string to escape.
 @return string The escaped string.
*/
function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); 
}

function formatDate(?string $iso): string {
  if (!$iso) return '';
  $dt = DateTime::createFromFormat('Y-m-d', $iso);
  return $dt ? $dt->format('d/m/Y') : e($iso);
}

function titolDificultat(?string $dif): string {
  if (!$dif) return '';
  $dif = mb_strtolower($dif, 'UTF-8');
  $map = [
    'facil'   => 'Fàcil',
    'mig'     => 'Mitjana',
    'dificil' => 'Difícil',
  ];

  if (array_key_exists($dif, $map)) {
    return $map[$dif];
  }

  // Fallback: capitalise in a multibyte-safe way
  return mb_convert_case($dif, MB_CASE_TITLE, 'UTF-8');
}

$pubId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pubId <= 0) {
  http_response_code(400);
  $errorMsg = "Falta l'ID de la publicació.";
}

$pub = null;
if (empty($errorMsg)) {
  try {
    $sql = "
      SELECT 
        e.*, 
        u.nom_usuari AS autor_username, u.nom AS autor_nom, u.cognom AS autor_cognom
      FROM excursio e
      LEFT JOIN usuari u ON u.id = e.id_usuari
      WHERE e.id = :id
      LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $pubId]);
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pub) {
      http_response_code(404);
      $errorMsg = "No s'ha trobat cap publicació amb ID $pubId.";
    }
  } catch (Throwable $ex) {
    // error_log($ex->getMessage());
    http_response_code(500);
    $errorMsg = "Error consultant la base de dades.";
  }
}

$pageTitle = $pub ? $pub['titol'] : 'Publicació';

// Deriva nom autor
$autorNomComplet = 'Usuari';
if ($pub) {
  if (!empty($pub['autor_nom']) || !empty($pub['autor_cognom'])) {
    $autorNomComplet = trim(($pub['autor_nom'] ?? '') . ' ' . ($pub['autor_cognom'] ?? ''));
  } elseif (!empty($pub['autor_username'])) {
    $autorNomComplet = $pub['autor_username'];
  }
}

$nomCim  = $pub['nom_cim']  ?? '';
$alcada  = $pub['alcada']   ?? '';
$comarca = $pub['comarca']  ?? '';

$img = $pub['imatges'] ?? '';
$imgSrc = $img ? 'uploads/'.basename($img) : 'img/placeholder.jpg';
?>
<!DOCTYPE html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($pageTitle) ?> · Publicació</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
  </head>

  <body data-publicacio-id="<?= e((string)$pubId) ?>">
    <header class="perfil__banner">
      <div class="contenedor">
        <div class="rejilla-2-1">
          <div class="perfil__saludo">
            <h1 class="seccion__titulo inicio-destacados__titulo">
              <?= $pub ? e($pub['titol']) : 'Publicació' ?>
            </h1>
            <?php
            if (empty($errorMsg)) {
              echo '<a href="perfil.php" class="publicacion__autor">Per ' . e($autorNomComplet) . '</a>';
            } else {
              echo '<span class="publicacion__autor">—</span>';
            }
            ?>
          </div>
          <div class="publicacion__like">
            <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
          </div>
        </div>
      </div>
    </header>

    <section class="seccion seccion--suave">
      <div class="contenedor">

        <?php if (!empty($errorMsg)): ?>
          <div class="alerta alerta--error" role="alert" style="margin:1rem 0">
            <?= e($errorMsg) ?> <a href="index.php" class="enlace">Tornar a l'inici</a>
          </div>
        <?php else: ?>

          <?php if ($alcada !== '' || $comarca !== '' || $nomCim !== ''): ?>
            <p><strong>· Cim:</strong> <?= e($nomCim) ?></p>
            <p><strong>· Alçada:</strong> <?= e((string)$alcada) ?> m</p>
            <p><strong>· Comarca:</strong> <?= e($comarca) ?></p>
          <?php endif; ?>

          <div class="seccion">
            <p><?= nl2br(e($pub ? ($pub['descripcio'] ?? '') : '')) ?></p>
          </div>

          <div class="seccion">
            <img class="publicacion__imagen" src="<?= e($imgSrc) ?>" alt="<?= e('Imatge de la publicació') ?>" />
          </div>

          <div class="seccion">
            <h2>Fitxa tècnica:</h2>
            <ul>
              <li><strong>Distància:</strong> <?= isset($pub['distancia']) ? (int)$pub['distancia'] . ' km' : '—' ?></li>
              <li><strong>Durada:</strong> <?= e($pub['temps_ruta'] ?? '') ?></li>
              <li><strong>Dificultat:</strong> <?= e(titolDificultat($pub['dificultat'] ?? '')) ?></li>
              <li><strong>Data:</strong> <?= e(formatDate($pub['data'] ?? '')) ?></li>
            </ul>
          </div>

          <!-- Comentaris: placeholder fins que ho connectis -->
          <div class="seccion">
            <div class="comentaris">
              <h2>Comentaris</h2>
              <form action="#" method="post">
                <label for="comentari">Deixa el teu comentari:</label>
                <textarea id="comentari" name="comentari" rows="2" placeholder="Escriu aquí..." required></textarea>
                <button type="submit">Enviar comentari</button>
              </form>

              <div class="llista-comentaris">
                <div class="comentari">
                  <div>
                    <span class="comentari__autor"><?= e($autorNomComplet) ?></span>
                    <span class="comentari__data">· <?= e(formatDate($pub['data'] ?? '')) ?></span>
                  </div>
                  <p class="comentari__text">
                    Gran sortida! 👌 (Aquesta secció la connectarem a la BD més endavant.)
                  </p>
                </div>
              </div>
            </div>
          </div>

        <?php endif; ?>

      </div>
    </section>

    <footer class="pie">
      <div class="contenedor pie__contenido">
        <small>© 2025 CIMSCAT</small>
        <nav class="pie__enlaces" aria-label="Enlaces legales">
          <a href="/privacidad.html">Política de privacidad</a>
          <a href="/contacto.html">Contacto</a>
        </nav>
      </div>
    </footer>
  </body>
</html>
