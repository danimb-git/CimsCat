<?php
session_start();
// Si ya hay sesión, no tiene sentido mostrar el login
if (!empty($_SESSION['user_id'])) {
  header('Location: /perfil.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pàgina de Login</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
</head>
<body>
  <section class="seccion login">
    <div class="contenedor">
      <h1 class="seccion__titulo login__titulo">Inicia la sessió</h1>

      <?php if (!empty($_GET['e'])): ?>
        <p class="alerta alerta--error">
          <?= [
            'missing'   => 'Omple tots els camps.',
            'invalid'   => 'Credencials incorrectes.',
            'required'  => 'Cal iniciar sessió per accedir.',
            'logoutok'  => 'Sessió tancada correctament.',
            'registered'=> 'Compte creat! Ja pots iniciar sessió.',
          ][$_GET['e']] ?? 'Hi ha hagut un error.' ?>
        </p>
      <?php endif; ?>

      <div class="login__card">
        <form action="/process_login.php" method="POST" class="login__form" novalidate>
          <div class="login__grupo">
            <label for="identifier" class="login__label">Usuari o correu</label>
            <input
              type="text"
              id="identifier"
              name="identifier"
              class="login__input"
              autocomplete="username"
              required
              autofocus
            />
          </div>

          <div class="login__grupo">
            <label for="password" class="login__label">Contrasenya</label>
            <input
              type="password"
              id="password"
              name="password"
              class="login__input"
              autocomplete="current-password"
              required
            />
          </div>

          <div class="login__acciones">
            <button type="submit" class="boton boton--marca">Iniciar sessió</button>
            <a href="register.php" class="boton boton--contraste" role="button">Crear un nou compte</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
