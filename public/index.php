<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ca">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inici</title>
  <link rel="stylesheet" href="css/01-base.css" />
  <link rel="stylesheet" href="css/02-layout.css" />
  <link rel="stylesheet" href="css/03-componentes.css" />
  <link rel="stylesheet" href="css/04-paginas.css" />
</head>

<body>
  <header class="portada">
    <div class="portada__grande">
      <img class="portada__imagen" src="./img/banner.jpg" alt="Panorámica de montañas" />
    </div>
    <h1 class="portada__titulo">CimsCat</h1>
  </header>

  <div class="barra">
    <div class="contenedor barra__contenido">
      <div class="barra__grupo">
        <input class="entrada" type="search" placeholder="Buscar...">
        <button class="boton">Buscar</button>
      </div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?>
          <a class="boton" href="perfiladministrador.php">Perfil</a>
        <?php else: ?>
          <a class="boton" href="perfil.php">Perfil</a>
        <?php endif; ?>
      <?php else: ?>
        <a class="boton" href="login.php">Iniciar sesión</a>
      <?php endif; ?>
    </div>
  </div>


  <section class="seccion seccion--suave">
    <div class="contenedor">
      <h2 class="seccion__titulo inicio-destacados__titulo">Excurcions destacades</h2>

      <div class="rejilla-3 inicio-destacados__rejilla">

        <article class="tarjeta">
          <img class="tarjeta__imagen" src="./img/montserrat.jpg" alt="Imatge de Montserrat">
          <div class="tarjeta__encabezado">
            <h3 class="tarjeta__titulo">Excursió a Montserrat</h3>
            <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
          </div>
          <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Montserrat.</p>
          <a class="tarjeta__cta" href="publicacio.html">Llegir més</a>
        </article>

        <article class="tarjeta">
          <img class="tarjeta__imagen" src="./img/cadiretes.jpg" alt="Imatge de Cadiretes">
          <div class="tarjeta__encabezado">
            <h3 class="tarjeta__titulo">Excursió a Cadiretes</h3>
            <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
          </div>
          <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Cadiretes.</p>
          <a class="tarjeta__cta" href="publicacio.html">Llegir més</a>
        </article>

        <article class="tarjeta">
          <img class="tarjeta__imagen" src="./img/puigmal.jpg" alt="Imatge de Puigmal">
          <div class="tarjeta__encabezado">
            <h3 class="tarjeta__titulo">Excursió a Puigmal</h3>
            <button class="boton-corazon" aria-label="Afegir als preferits">♥</button>
          </div>
          <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Puigmal.</p>
          <a class="tarjeta__cta" href="publicacio.html">Llegir més</a>
        </article>

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