<?php 
session_start(); 

// Importar models necessaris
require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/models/Like.php';

// Obtenir connexió a la base de dades
$database = new Database();
$conn = $database->getConnection();

// Query per obtenir les 3 excursions amb més likes
$query = "SELECT 
    e.id,
    e.titol,
    e.descripcio,
    e.imatges,
    COUNT(l.id) as total_likes
FROM excursio e
LEFT JOIN `like` l ON e.id = l.id_excursio
GROUP BY e.id, e.titol, e.descripcio, e.imatges
ORDER BY total_likes DESC, e.created_at DESC
LIMIT 3";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $excursions_destacades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si l'usuari està logejat, comprovar quines excursions ja tenen like
    if (isset($_SESSION['user_id'])) {
        $like_model = new Like();
        foreach ($excursions_destacades as &$excursio) {
            $like_model->id_excursio = $excursio['id'];
            $like_model->id_usuari = $_SESSION['user_id'];
            $excursio['user_liked'] = $like_model->exists();
        }
        unset($excursio); // Trencar referència
    }
} catch (PDOException $e) {
    // Si hi ha error, usar array buit
    $excursions_destacades = [];
    error_log("Error carregant excursions: " . $e->getMessage());
}

// Si no hi ha excursions a la BD, usar les 3 per defecte (estàtiques)
$usar_estatiques = empty($excursions_destacades);
?>
<!DOCTYPE html>
<html lang="ca">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inici - CimsCat</title>
  <link rel="stylesheet" href="css/01-base.css" />
  <link rel="stylesheet" href="css/02-layout.css" />
  <link rel="stylesheet" href="css/03-componentes.css" />
  <link rel="stylesheet" href="css/04-paginas.css" />
  <link rel="stylesheet" href="css/likes-comentaris.css" />
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
        <a class="boton" href="perfil.php">Perfil</a>
      <?php else: ?>
        <a class="boton" href="login.php">Iniciar sesión</a>
      <?php endif; ?>
    </div>
  </div>

  <section class="seccion seccion--suave">
    <div class="contenedor">
      <h2 class="seccion__titulo inicio-destacados__titulo">
        <?php echo $usar_estatiques ? 'Excursions destacades' : 'Excursions més populars'; ?>
      </h2>
      <?php if (!$usar_estatiques): ?>
        <p style="text-align: center; color: #666; margin-top: -1rem; margin-bottom: 2rem;">
          Les excursions amb més m'agrada de la comunitat
        </p>
      <?php endif; ?>

      <div class="rejilla-3 inicio-destacados__rejilla">

        <?php if (!$usar_estatiques): ?>
          <!-- EXCURSIONS DE LA BASE DE DADES -->
          <?php foreach ($excursions_destacades as $excursio): ?>
            <article class="tarjeta">
              <img class="tarjeta__imagen" 
                   src="./img/<?php echo htmlspecialchars($excursio['imatges'] ?? 'placeholder.jpg'); ?>" 
                   alt="Imatge de <?php echo htmlspecialchars($excursio['titol']); ?>"
                   onerror="this.src='./img/placeholder.jpg'">
              
              <div class="tarjeta__encabezado">
                <h3 class="tarjeta__titulo"><?php echo htmlspecialchars($excursio['titol']); ?></h3>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                  <!-- Usuari logejat: pot fer like -->
                  <button class="boton-corazon <?php echo ($excursio['user_liked'] ?? false) ? 'activo' : ''; ?>" 
                          data-excursio-id="<?php echo $excursio['id']; ?>"
                          aria-label="Afegir als preferits">
                    ♥
                    <span class="like-count"><?php echo $excursio['total_likes']; ?></span>
                  </button>
                <?php else: ?>
                  <!-- Usuari NO logejat: només mostra comptador i redirigeix al login -->
                  <a href="login.php" 
                     class="boton-corazon boton-corazon--disabled" 
                     title="Inicia sessió per donar m'agrada"
                     aria-label="Inicia sessió per donar m'agrada">
                    ♥
                    <span class="like-count"><?php echo $excursio['total_likes']; ?></span>
                  </a>
                <?php endif; ?>
              </div>
              
              <p class="tarjeta__texto">
                <?php 
                  $descripcio = $excursio['descripcio'] ?? 'Descobreix aquesta ruta increïble.';
                  echo htmlspecialchars(
                    strlen($descripcio) > 80 ? 
                    substr($descripcio, 0, 80) . '...' : 
                    $descripcio
                  ); 
                ?>
              </p>
              
              <a class="tarjeta__cta" href="publicacio.php?id=<?php echo $excursio['id']; ?>">Llegir més</a>
            </article>
          <?php endforeach; ?>

        <?php else: ?>
          <!-- EXCURSIONS ESTÀTIQUES (quan no hi ha a la BD) -->
          <article class="tarjeta">
            <img class="tarjeta__imagen" src="./img/montserrat.jpg" alt="Imatge de Montserrat">
            <div class="tarjeta__encabezado">
              <h3 class="tarjeta__titulo">Excursió a Montserrat</h3>
              <?php if (isset($_SESSION['user_id'])): ?>
                <button class="boton-corazon" data-excursio-id="1" aria-label="Afegir als preferits">
                  ♥<span class="like-count">0</span>
                </button>
              <?php else: ?>
                <a href="login.php" class="boton-corazon boton-corazon--disabled" title="Inicia sessió per donar m'agrada">
                  ♥<span class="like-count">0</span>
                </a>
              <?php endif; ?>
            </div>
            <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Montserrat.</p>
            <a class="tarjeta__cta" href="publicacio.php?id=1">Llegir més</a>
          </article>

          <article class="tarjeta">
            <img class="tarjeta__imagen" src="./img/cadiretes.jpg" alt="Imatge de Cadiretes">
            <div class="tarjeta__encabezado">
              <h3 class="tarjeta__titulo">Excursió a Cadiretes</h3>
              <?php if (isset($_SESSION['user_id'])): ?>
                <button class="boton-corazon" data-excursio-id="2" aria-label="Afegir als preferits">
                  ♥<span class="like-count">0</span>
                </button>
              <?php else: ?>
                <a href="login.php" class="boton-corazon boton-corazon--disabled" title="Inicia sessió per donar m'agrada">
                  ♥<span class="like-count">0</span>
                </a>
              <?php endif; ?>
            </div>
            <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Cadiretes.</p>
            <a class="tarjeta__cta" href="publicacio.php?id=2">Llegir més</a>
          </article>

          <article class="tarjeta">
            <img class="tarjeta__imagen" src="./img/puigmal.jpg" alt="Imatge de Puigmal">
            <div class="tarjeta__encabezado">
              <h3 class="tarjeta__titulo">Excursió a Puigmal</h3>
              <?php if (isset($_SESSION['user_id'])): ?>
                <button class="boton-corazon" data-excursio-id="3" aria-label="Afegir als preferits">
                  ♥<span class="like-count">0</span>
                </button>
              <?php else: ?>
                <a href="login.php" class="boton-corazon boton-corazon--disabled" title="Inicia sessió per donar m'agrada">
                  ♥<span class="like-count">0</span>
                </a>
              <?php endif; ?>
            </div>
            <p class="tarjeta__texto">Descobreix els millors camins per gaudir de la muntanya de Puigmal.</p>
            <a class="tarjeta__cta" href="publicacio.php?id=3">Llegir més</a>
          </article>
        <?php endif; ?>

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

  <!-- SCRIPTS PER LIKES (només si l'usuari està logejat) -->
  <?php if (isset($_SESSION['user_id'])): ?>
  <script>
    window.USER_ID = <?php echo $_SESSION['user_id']; ?>;
    window.USER_ROL = '<?php echo $_SESSION['rol'] ?? 'usuari'; ?>';
  </script>
  <script src="js/likes-comentaris.js"></script>
  <?php endif; ?>
</body>

</html>