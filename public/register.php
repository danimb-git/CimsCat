<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pàgina de Registre</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
</head>

<body>
    <section class="seccion login">
        <div class="contenedor">
            <h1 class="seccion__titulo login__titulo">Crear un nou compte</h1>

            <?php if (!empty($_GET['e'])): ?>
              <p class="alerta alerta--error">
                <?= [
                    'missing'    => 'Omple tots els camps obligatoris.',
                    'badmail'    => 'Correu electrònic invàlid.',
                    'exists'     => 'El nom d’usuari o el correu ja existeixen.',
                    'pw_mismatch'=> 'Les contrasenyes no coincideixen.',
                    'dberr'      => 'Error guardant a la base de dades.',
                    'registered' => 'Compte creat! Ja pots iniciar sessió.',
                ][$_GET['e']] ?? 'Hi ha hagut un error.' ?>
              </p>
            <?php endif; ?>

            <div class="login__card">
                <form action="/process_register.php" method="post" class="login__form" novalidate>
                    <div class="login__grupo">
                        <label for="nom_usuari" class="login__label">Nom d'usuari</label>
                        <input type="text" id="nom_usuari" name="nom_usuari"
                               class="login__input" autocomplete="username"
                               required maxlength="25" />
                    </div>

                    <div class="login__grupo">
                        <label for="nom" class="login__label">Nom</label>
                        <input type="text" id="nom" name="nom"
                               class="login__input" required maxlength="25" />
                    </div>

                    <div class="login__grupo">
                        <label for="cognom" class="login__label">Cognom</label>
                        <input type="text" id="cognom" name="cognom"
                               class="login__input" required maxlength="50" />
                    </div>

                    <div class="login__grupo">
                        <label for="mail" class="login__label">Correu electrònic</label>
                        <input type="email" id="mail" name="mail"
                               class="login__input" required maxlength="75" autocomplete="email" />
                    </div>

                    <div class="login__grupo">
                        <label for="password" class="login__label">Contrasenya</label>
                        <input type="password" id="password" name="password"
                               class="login__input" required autocomplete="new-password" />
                    </div>

                    <div class="login__grupo">
                        <label for="password2" class="login__label">Repeteix la contrasenya</label>
                        <input type="password" id="password2" name="password2"
                               class="login__input" required autocomplete="new-password" />
                    </div>

                    <div class="login__grupo">
                        <label for="edat" class="login__label">Edat</label>
                        <input type="number" id="edat" name="edat"
                               class="login__input" min="0" max="120" />
                    </div>

                    <div class="login__acciones">
                        <button type="submit" class="boton boton--marca">Crear un nou compte</button>
                        <a href="/login.php" class="boton boton--contraste">Ja tinc compte</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Validació client lleugera per “password == password2” (opcional) -->
    <script>
      const form = document.querySelector('form');
      form?.addEventListener('submit', (e) => {
        const p1 = document.getElementById('password')?.value || '';
        const p2 = document.getElementById('password2')?.value || '';
        if (p1 !== p2) {
          e.preventDefault();
          alert('Les contrasenyes no coincideixen.');
        }
      });
    </script>
</body>
</html>
