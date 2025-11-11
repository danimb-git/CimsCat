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
                <div>
                    <h2 class="seccion__titulo">Excursions guardades</h2>

                    <div class="rejilla-2 inicio-destacados__rejilla">
                        <article class="tarjeta">
                            <img class="tarjeta__imagen" src="./img/montserrat.jpg" alt="Imatge de Montserrat">
                            <div class="tarjeta__encabezado">
                                <h3 class="tarjeta__titulo">Excursió a Montserrat</h3>
                                <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
                            </div>
                            <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de
                                Montserrat.
                            </p>
                            <a class="tarjeta__cta" href="publicacio.php">Llegir més</a>
                        </article>
                        <article class="tarjeta">
                            <img class="tarjeta__imagen" src="./img/cadiretes.jpg" alt="Imatge de Cadiretes">
                            <div class="tarjeta__encabezado">
                                <h3 class="tarjeta__titulo">Excursió a Cadiretes</h3>
                                <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
                            </div>
                            <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de
                                Cadiretes.
                            </p>
                            <a class="tarjeta__cta" href="publicacio.php">Llegir més</a>
                        </article>
                    </div>
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