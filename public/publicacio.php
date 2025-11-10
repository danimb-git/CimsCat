<?php
// public/publicacio.php
// No requerim login; només llegim l'ID si arriba per GET.
session_start();
$pubId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Nota: De moment no fem servir $pubId. Es queda per quan vulguis fer-ho dinàmic.
?>
<!DOCTYPE html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pàgina de Publicació</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
  </head>

  <!-- Deixem guardat l'ID per si el vols fer servir en JS més endavant -->
  <body data-publicacio-id="<?= htmlspecialchars((string)$pubId) ?>">
    <header class="perfil__banner">
      <div class="contenedor">
        <div class="rejilla-2-1">
          <div class="perfil__saludo">
            <h1 class="seccion__titulo inicio-destacados__titulo">
              Pica d'Estats per cara nord
            </h1>
            <!-- Enllaç actualitzat a .php; el contingut segueix sent inventat -->
            <a href="perfil.php" class="publicacion__autor">Per Dàlia Jordan</a>
          </div>
          <div class="publicacion__like">
            <button class="boton-corazon" aria-label="Afegir als preferits">
              ♥
            </button>
          </div>
        </div>
      </div>
    </header>

    <section class="seccion seccion--suave">
      <div class="contenedor">
        <p><strong>· Alçada: 3.133 m</strong></p>
        <p><strong>· Comarca: Pallars Sobirà</strong></p>

        <div class="seccion">
          <p>
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nihil
            voluptates fugit incidunt asperiores voluptate qui. Maxime doloribus
            nihil sequi ipsum facilis expedita praesentium vel dolorum
            repellendus. Labore ut dignissimos nam! Lorem ipsum dolor sit amet
            consectetur adipisicing elit. Officiis quas, distinctio magni culpa
            sunt dolor sequi voluptatibus iste debitis dolorum ratione non,
            exercitationem sapiente mollitia aliquid obcaecati corrupti, ab
            voluptas? Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Harum voluptatum, provident rem alias aspernatur corporis nam
            voluptates velit voluptas labore amet accusamus unde cupiditate
            sequi fugiat pariatur! Corporis, excepturi nulla? Lorem ipsum, dolor
            sit amet consectetur adipisicing elit. Aspernatur nihil cumque quis
            totam id nostrum laborum aperiam similique et architecto, eos culpa!
            Laborum architecto officia non, sequi rerum laboriosam
            necessitatibus!
          </p>
          <p>
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nihil
            voluptates fugit incidunt asperiores voluptate qui. Maxime doloribus
            nihil sequi ipsum facilis expedita praesentium vel dolorum
            repellendus. Labore ut dignissimos nam! Lorem ipsum dolor sit amet
            consectetur adipisicing elit. Officiis quas, distinctio magni culpa
            sunt dolor sequi voluptatibus iste debitis dolorum ratione non,
            exercitationem sapiente mollitia aliquid obcaecati corrupti, ab
            voluptas? Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Harum voluptatum, provident rem alias aspernatur corporis nam
            voluptates velit voluptas labore amet accusamus unde cupiditate
            sequi fugiat pariatur! Corporis, excepturi nulla? Lorem ipsum, dolor
            sit amet consectetur adipisicing elit. Aspernatur nihil cumque quis
            totam id nostrum laborum aperiam similique et architecto, eos culpa!
            Laborum architecto officia non, sequi rerum laboriosam
            necessitatibus!
          </p>
        </div>

        <div class="seccion">
          <img
            class="publicacion__imagen"
            src="./img/montserrat.jpg"
            alt="Imatge de Montserrat"
          />
        </div>

        <div class="seccion">
          <h2>Fitxa tècnica:</h2>
          <ul>
            <li>Desnivell: 1.200 m</li>
            <li>Distància: 12 km</li>
            <li>Durada: 6 hores</li>
            <li>Dificultat: Alta</li>
            <li>Data: 12/12/2025</li>
          </ul>
        </div>

        <div class="seccion">
          <div class="comentaris">
            <h2>Comentaris</h2>
            <form action="#" method="post">
              <label for="comentari">Deixa el teu comentari:</label>
              <textarea
                id="comentari"
                name="comentari"
                rows="2"
                placeholder="Escriu aquí..."
                required
              ></textarea>
              <button type="submit">Enviar comentari</button>
            </form>

            <div class="llista-comentaris">
              <div class="comentari">
                <div>
                  <span class="comentari__autor">Mireia Gibert</span>
                  <span class="comentari__data">· 12/12/2025</span>
                </div>
                <p class="comentari__text">
                  Ruta espectacular! El tram final és dur però val la pena les
                  vistes!
                </p>
              </div>

              <div class="comentari">
                <div>
                  <span class="comentari__autor">Dani Moore</span>
                  <span class="comentari__data">· 13/12/2025</span>
                </div>
                <p class="comentari__text">
                  Bon resum, Dàlia! Jo també hi vaig pujar aquest estiu i el
                  record és brutal 😍
                </p>
              </div>
            </div>
          </div>
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

    <!-- (Opcional) Si algun dia vols fer servir l'ID des de JS:
    <script>
      const pubId = document.body.dataset.publicacioId;
      // console.log('Publicació ID:', pubId);
    </script>
    -->
  </body>
</html>
