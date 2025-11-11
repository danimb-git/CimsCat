<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: /login.php?e=required');
    exit;
}
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

$stmt = $pdo->prepare("SELECT nom, cognom, nom_usuari, mail, foto FROM usuari WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('Location: /logout.php');
    exit;
}


$avatar = !empty($user['foto']) ? $user['foto'] : 'uploads/avatars/default.png';
$nomComplet = trim(($user['nom'] ?? '') . ' ' . ($user['cognom'] ?? ''));

// (a) PUBLICACIONS GUARDADES PER LIKE ── recórrer la taula `like` filtrant per l'usuari loguejat
$sqlLikes = "
  SELECT 
      e.id          AS id_excursio,
      e.titol,
      e.imatges,
      e.dificultat,
      COALESCE(c.nom, e.cim_nom) AS nom_cim   -- si heu 'desacoblat' el cim, agafem e.cim_nom
  FROM `like` l
  JOIN excursio e       ON e.id = l.id_excursio
  LEFT JOIN cim c       ON c.id = e.id_cim
  WHERE l.id_usuari = ?
  ORDER BY l.created_at DESC
";
$stmt = $pdo->prepare($sqlLikes);
$stmt->execute([$_SESSION['user_id']]);
$guardades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// (b) PUBLICACIONS CREADES PER L’USUARI ── recórrer la taula `excursio` filtrant per propietari
$sqlMine = "
  SELECT
      e.id          AS id_excursio,
      e.titol,
      e.imatges,
      e.dificultat,
      COALESCE(c.nom, e.cim_nom) AS nom_cim
  FROM excursio e
  LEFT JOIN cim c ON c.id = e.id_cim
  WHERE e.id_usuari = ?
  ORDER BY e.created_at DESC
";
$stmt = $pdo->prepare($sqlMine);
$stmt->execute([$_SESSION['user_id']]);
$meves = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funció per comprovar si l'usuari actual ha fet like a una excursió
function usuariHaFetLike($pdo, $id_excursio, $id_usuari) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `like` WHERE id_excursio = ? AND id_usuari = ?");
    $stmt->execute([$id_excursio, $id_usuari]);
    return $stmt->fetchColumn() > 0;
}

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
    <link rel="stylesheet" href="css/likes-comentaris.css" />
    <style>
        .rejilla-2.inicio-destacados__rejilla {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        @media (max-width: 800px) {
            .rejilla-2.inicio-destacados__rejilla {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="perfil__banner">
  <div class="contenedor">
    <div class="rejilla-2-1">
      <div class="perfil__saludo">
        <!-- 🔸 Nou botó per tornar a l'inici -->
        <a class="boton boton--marca" href="index.php" style="margin-bottom: 1rem; display: inline-block;">
          🏠 Tornar a l'inici
        </a>

        <h1>Hola,<br><?= htmlspecialchars($nomComplet) ?></h1>
      </div>

      <div class="perfil__avatar">
        <img class="perfil__avatar" src="/<?= htmlspecialchars($avatar) ?>" alt="Foto de perfil">
      </div>
    </div>
  </div>
</header>


    <section class="seccion seccion--suave">
        <div class="contenedor">
            <div class="rejilla-2-1">

                <!-- 🔹 COLUMNA ESQUERRA -->
                <div class="perfil__col-izq">

                    <!-- Excursions guardades -->
                    <h2 class="seccion__titulo">Excursions guardades</h2>

                    <?php if (empty($guardades)): ?>
                        <p class="muted">Encara no has guardat cap excursió. Ves a les publicacions i prem el cor per desar-les aquí.</p>
                    <?php else: ?>
                        <div class="carousel" data-section="guardades">
                            <button class="carousel__btn prev" aria-label="Anterior">‹</button>
                            <div class="carousel__track">
                                <?php foreach ($guardades as $row):
                                    $titol  = htmlspecialchars($row['titol'] ?? '—', ENT_QUOTES, 'UTF-8');
                                    $nomCim = htmlspecialchars($row['nom_cim'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $map    = ['facil' => 'Fàcil', 'mig' => 'Mitjana', 'dificil' => 'Difícil'];
                                    $dif    = $map[$row['dificultat'] ?? ''] ?? '';
                                    $idExc  = (int)$row['id_excursio'];
                                    $img = trim((string)($row['imatges'] ?? ''));
                                    if ($img === '') $imgSrc = 'img/placeholder.jpg';
                                    else $imgSrc = (str_starts_with($img, 'uploads/')) ? $img : ('uploads/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8'));
                                ?>
                                    <article class="tarjeta carousel__item">
                                        <img class="tarjeta__imagen" src="<?= $imgSrc ?>" alt="Imatge de <?= $titol ?>">
                                        <div class="tarjeta__encabezado">
                                            <h3 class="tarjeta__titulo"><?= $titol ?></h3>
                                            <?php $haLike = usuariHaFetLike($pdo, $idExc, $_SESSION['user_id']); ?>
                                            <button class="boton-corazon <?= $haLike ? 'activo' : '' ?>"
                                                data-excursio-id="<?= $idExc ?>"
                                                aria-label="<?= $haLike ? 'Eliminar dels preferits' : 'Afegir als preferits' ?>">
                                                ♥ <span class="like-count">0</span>
                                            </button>
                                        </div>
                                        <p class="tarjeta__texto"><?= $nomCim ?><?= ($nomCim && $dif) ? ' · ' : '' ?><?= $dif ?></p>
                                        <a class="tarjeta__cta" href="publicacio.php?id=<?= $idExc ?>">Llegir més</a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel__btn next" aria-label="Següent">›</button>
                        </div>
                    <?php endif; ?>

                    <!-- Les meves publicacions -->
                    <h2 class="seccion__titulo" style="margin-top:2rem">Les meves publicacions</h2>

                    <?php if (empty($meves)): ?>
                        <p class="muted">Encara no has creat cap publicació. Crea’n una des de “Nova publicació”.</p>
                    <?php else: ?>
                        <div class="carousel" data-section="meves">
                            <button class="carousel__btn prev" aria-label="Anterior">‹</button>
                            <div class="carousel__track">
                                <?php foreach ($meves as $row):
                                    $titol  = htmlspecialchars($row['titol'] ?? '—', ENT_QUOTES, 'UTF-8');
                                    $nomCim = htmlspecialchars($row['nom_cim'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $map    = ['facil' => 'Fàcil', 'mig' => 'Mitjana', 'dificil' => 'Difícil'];
                                    $dif    = $map[$row['dificultat'] ?? ''] ?? '';
                                    $idExc  = (int)$row['id_excursio'];
                                    $img = trim((string)($row['imatges'] ?? ''));
                                    if ($img === '') $imgSrc = 'img/placeholder.jpg';
                                    else $imgSrc = (str_starts_with($img, 'uploads/')) ? $img : ('uploads/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8'));
                                ?>
                                    <article class="tarjeta carousel__item">
                                        <img class="tarjeta__imagen" src="<?= $imgSrc ?>" alt="Imatge de <?= $titol ?>">
                                        <div class="tarjeta__encabezado">
                                            <h3 class="tarjeta__titulo"><?= $titol ?></h3>
                                            <?php $haLike = usuariHaFetLike($pdo, $idExc, $_SESSION['user_id']); ?>
                                            <button class="boton-corazon <?= $haLike ? 'activo' : '' ?>"
                                                data-excursio-id="<?= $idExc ?>"
                                                aria-label="<?= $haLike ? 'Eliminar dels preferits' : 'Afegir als preferits' ?>">
                                                ♥ <span class="like-count">0</span>
                                            </button>
                                        </div>
                                        <p class="tarjeta__texto"><?= $nomCim ?><?= ($nomCim && $dif) ? ' · ' : '' ?><?= $dif ?></p>
                                        <a class="tarjeta__cta" href="publicacio.php?id=<?= $idExc ?>">Veure publicació</a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel__btn next" aria-label="Següent">›</button>
                        </div>
                    <?php endif; ?>

                </div><!-- /.perfil__col-izq -->

                <!-- 🔹 COLUMNA DRETA -->
                <aside class="perfil__lateral">
                    <div class="perfil__lateral_superior">
                        <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'administrador'): ?>
                            <a class="boton boton--marca" href="perfiladministrador.php">Panell de control</a>
                        <?php endif; ?>

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

    <!-- Variables globals per al JS de likes -->
    <script>
        <?php if (isset($_SESSION['user_id'])): ?>
            window.USER_ID = <?= (int)$_SESSION['user_id'] ?>;
            window.USER_ROL = '<?= htmlspecialchars($_SESSION['rol'] ?? 'usuari', ENT_QUOTES, 'UTF-8') ?>';
        <?php endif; ?>
    </script>

    <script src="js/likes-comentaris.js"></script>

    <!-- CSS compacte per targetes petites i carrousel -->
    <style>
        .carousel {
            position: relative;
            margin-block: 1rem 2rem;
            overflow: hidden;
        }

        .carousel__track {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            transition: all .3s ease;
        }

        .carousel__item {
            flex: 0 0 calc(33.33% - 1rem);
            max-width: 320px;
        }

        @media (max-width: 900px) {
            .carousel__item {
                flex: 0 0 calc(50% - 1rem);
            }
        }

        @media (max-width: 600px) {
            .carousel__item {
                flex: 0 0 100%;
            }
        }

        .carousel__btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            inline-size: 2rem;
            block-size: 2rem;
            border-radius: 50%;
            border: 1px solid #795548;
            background: #fff;
            cursor: pointer;
            font-size: 1.25rem;
            font-weight: bold;
            line-height: 2rem;
            text-align: center;
            z-index: 2;
        }

        .carousel__btn.prev {
            left: .25rem;
        }

        .carousel__btn.next {
            right: .25rem;
        }
    </style>

    <!-- JS del carrousel amb paginació real (3 targetes per pàgina) -->
    <script>
        document.querySelectorAll('.carousel').forEach(carousel => {
            const track = carousel.querySelector('.carousel__track');
            const items = carousel.querySelectorAll('.carousel__item');
            const prev = carousel.querySelector('.carousel__btn.prev');
            const next = carousel.querySelector('.carousel__btn.next');

            const itemsPerPage = 3;
            let currentPage = 0;

            function showPage(page) {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                if (page < 0) page = 0;
                if (page >= totalPages) page = totalPages - 1;
                currentPage = page;

                items.forEach((item, index) => {
                    item.style.display = (index >= page * itemsPerPage && index < (page + 1) * itemsPerPage) ?
                        "block" :
                        "none";
                });

                prev.disabled = (currentPage === 0);
                next.disabled = (currentPage === totalPages - 1);
            }

            prev.addEventListener("click", () => showPage(currentPage - 1));
            next.addEventListener("click", () => showPage(currentPage + 1));

            showPage(0);
        });
    </script>
</body>


</html>