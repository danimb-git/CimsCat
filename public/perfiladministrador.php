<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: /login.php?e=required'); exit; }
if (($_SESSION['rol'] ?? '') !== 'administrador') {
  header('Location: /perfil.php'); exit;
}

require_once __DIR__ . '/../src/config/Database.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function formatDate(?string $iso): string {
  if (!$iso) return '';
  $dt = DateTime::createFromFormat('Y-m-d', $iso);
  if (!$dt) { // si ve amb hores o altre format, intentem parsejar-lo igualment
    try { $dt = new DateTime($iso); } catch (\Throwable $e) { return htmlspecialchars($iso, ENT_QUOTES, 'UTF-8'); }
  }
  return $dt->format('d/m/Y');
}

function autorFromRow(array $ex): string {
  $nomCognom = trim(($ex['autor_nom'] ?? '') . ' ' . ($ex['autor_cognom'] ?? ''));
  if ($nomCognom !== '') return $nomCognom;
  return $ex['autor_username'] ?? '—';
}

$pdo = (new Database())->getConnection();

$stmtUser = $pdo->prepare("SELECT nom, cognom, nom_usuari, foto FROM usuari WHERE id = :id");
$stmtUser->execute([':id' => $_SESSION['user_id']]);
$me = $stmtUser->fetch(PDO::FETCH_ASSOC) ?: [];

$avatar = !empty($me['foto']) ? $me['foto'] : 'uploads/avatars/default.png';
$nomComplet = trim(($me['nom'] ?? '') . ' ' . ($me['cognom'] ?? ''));
if ($nomComplet === '') { $nomComplet = $me['nom_usuari'] ?? 'Usuari'; }

// Carga excursiones
$sql = "SELECT
          e.id, e.titol, e.descripcio, e.data, e.temps_ruta, e.dificultat, 
          e.imatges, e.distancia, e.id_cim, e.id_usuari, e.created_at,
          u.nom        AS autor_nom,
          u.cognom     AS autor_cognom,
          u.nom_usuari AS autor_username,
          u.foto       AS autor_foto
        FROM excursio e
        LEFT JOIN usuari u ON u.id = e.id_usuari
        ORDER BY e.created_at DESC";
$stmt = $pdo->query($sql);
$excursions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pàgina de Perfil</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
</head>

<body>
    <header class="perfil__banner">
        <div class="contenedor">
            <div class="rejilla-2-1">
                <div class="perfil__saludo">
                    <h1>Hola,<br><?= e($nomComplet) ?></h1>
                </div>
                <div class="perfil__avatar">
                    <img src="<?= e($avatar) ?>" alt="Avatar d'usuari">
                </div>
            </div>
        </div>
    </header>


    <section class="seccion seccion--suave">
        <div class="contenedor">
            <div class="rejilla-2-1">
                <div>
                    <h2 class="seccion__titulo">Gestionar publicacions</h2>
                    
                    <?php
                    $ok = $_GET['ok'] ?? '';
                    $e  = $_GET['e']  ?? '';
                    if ($ok === 'deleted') {
                    echo '<div class="alerta alerta--ok">Publicació eliminada correctament.</div>';
                    } elseif ($e === 'notfound') {
                    echo '<div class="alerta alerta--warn">Aquesta publicació no existeix o ja s\'ha eliminat.</div>';
                    } elseif ($e === 'id') {
                    echo '<div class="alerta alerta--error">Identificador invàlid.</div>';
                    } elseif ($e === 'db') {
                    echo '<div class="alerta alerta--error">No s\'ha pogut eliminar per un error de base de dades.</div>';
                    } elseif ($e === 'method') {
                    echo '<div class="alerta alerta--warn">Operació no permesa (només POST).</div>';
                    }
                    ?>

                    <?php if (empty($excursions)): ?>
                        <div class="empty">No hi ha cap excursió registrada.</div>
                    <?php else: ?>
                        <table class="tabla">
                            <?php foreach ($excursions as $ex): ?>
                                <tr>
                                    <td>
                                        <?= e($ex['titol'] ?? '') ?>
                                        - <?= e(autorFromRow($ex)) ?>
                                        - <?= e(formatDate($ex['data'] ?? '')) ?>
                                    </td>
                                    <td class="tabla__derecha">
                                        <a class="boton boton--marca" href="/editar_excursio.php?id=<?= (int)$ex['id'] ?>">Editar</a>

                                        <form action="/process_eliminar_excursio.php" method="post" onsubmit="return confirm('Vols eliminar aquesta publicació?');" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= (int)$ex['id'] ?>">
                                            <button class="boton boton--contraste" type="submit">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>

                <aside class="perfil__lateral">
                    <div class="perfil__lateral_superior">
                        <a class="boton boton--contraste" href="editperfil.php">Editar perfil</a>
                        <a class="boton boton--marca" href="novapublicacio.php">Nova publicació</a>
                    </div>

                    <div class="perfil__lateral_inferior">
                        <a class="boton boton--marca" href="logout.php">Log Out</a>
                    </div>
                </aside>

            </div>
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