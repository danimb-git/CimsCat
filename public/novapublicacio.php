<?php session_start(); if (empty($_SESSION['user_id'])) { header('Location: /register.php?e=login'); exit; } ?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pàgina de nova publicació</title>
    <link rel="stylesheet" href="css/01-base.css" />
    <link rel="stylesheet" href="css/02-layout.css" />
    <link rel="stylesheet" href="css/03-componentes.css" />
    <link rel="stylesheet" href="css/04-paginas.css" />
</head>

<body>
    <section class="seccion login">
        <div class="contenedor">
            <h1 class="seccion__titulo login__titulo">
                Afegir una nova publicació
            </h1>

            <div class="login__card">
                <form action="publicacio.html" method="get" class="login__form" enctype="multipart/form-data"
                    novalidate>
                    <!-- Títol -->
                    <div class="login__grupo">
                        <label for="titol" class="login__label">Títol de la publicació *</label>
                        <input type="text" id="titol" name="titol" class="login__input" autocomplete="off" required
                            maxlength="120" />
                    </div>

                    <!-- Descripció -->
                    <div class="login__grupo">
                        <label for="descripcio" class="login__label">Descripció *</label>
                        <textarea id="descripcio" name="descripcio" class="login__textarea" rows="6"
                            required></textarea>
                    </div>

                    <!-- Nom del cim -->
                    <div class="login__grupo">
                        <label for="nom_cim" class="login__label">Nom del cim *</label>
                        <input type="text" id="nom_cim" name="nom_cim" class="login__input" autocomplete="off"
                            required />
                    </div>

                    <!-- Alçada (m) -->
                    <div class="login__grupo">
                        <label for="alcada" class="login__label">Alçada (m) *</label>
                        <input type="number" id="alcada" name="alcada" class="login__input" min="0" max="6000" step="1"
                            required />
                    </div>

                    <!-- Comarca -->
                    <div class="login__grupo">
                        <label for="comarca" class="login__label">Comarca *</label>
                        <select id="comarca" name="comarca" class="login__select" required></select>
                        <script>
                            const COMARQUES = ["Alt Camp","Alt Empordà","Alt Penedès","Alt Urgell","Alta Ribagorça","Anoia","Bages","Baix Camp","Baix Ebre","Baix Empordà","Baix Llobregat","Baix Penedès","Barcelonès","Berguedà","Cerdanya","Conca de Barberà","Garraf","Garrigues","Garrotxa","Gironès","Maresme","Moianès","Montsià","Noguera","Osona","Pallars Jussà","Pallars Sobirà","Pla d'Urgell","Pla de l'Estany","Priorat","Ribera d'Ebre","Ripollès","Segarra","Segrià","Selva","Solsonès","Tarragonès","Terra Alta","Urgell","Val d'Aran","Vallès Occidental","Vallès Oriental"];

                            const s = document.getElementById("comarca");
                            s.innerHTML =
                                "<option disabled selected>— Selecciona la comarca —</option>" +
                                COMARQUES.map((c) => `<option>${c}</option>`).join("") +
                                '<option value="altres">Altres…</option>';
                        </script>
                    </div>

                    <!-- Dificultat -->
                    <div class="login__grupo">
                        <label for="dificultat" class="login__label">Dificultat *</label>
                        <select id="dificultat" name="dificultat" class="login__select" required>
                            <option value="" selected disabled>— Selecciona —</option>
                            <option value="facil">Fàcil</option>
                            <option value="mig">Mig</option>
                            <option value="dificil">Difícil</option>
                        </select>
                    </div>

                    <!-- Distància (km) -->
                    <div class="login__grupo">
                        <label for="distancia" class="login__label">Distància (km) *</label>
                        <input type="number" id="distancia" name="distancia" class="login__input" min="0" step="0.1"
                            required />
                    </div>

                    <!-- Duració (hores:minuts) -->
                    <div class="login__grupo">
                        <label for="temps_ruta" class="login__label">Duració (hores:minuts) *</label>
                        <!-- input time retorna HH:MM -->
                        <input type="time" id="temps_ruta" name="temps_ruta" class="login__input" required />
                    </div>

                    <!-- Data de la publicació / de l'excursió -->
                    <div class="login__grupo">
                        <label for="data_publicacio" class="login__label">Data *</label>
                        <input type="date" id="data_publicacio" name="data_publicacio" class="login__input" required />
                    </div>

                    <!-- Imatges -->
                    <div class="login__grupo">
                        <label for="imatges" class="login__label">Imatges de la publicació (JPG/PNG, fins a 5)</label>
                        <input type="file" id="imatges" name="imatges[]" class="login__input" accept=".jpg,.jpeg,.png"
                            multiple />
                    </div>

                    <!-- ID usuari (omplir al servidor) -->
                    <input type="hidden" id="usuari_id" name="usuari_id" value="<?= $_SESSION['user_id'] ?? '' ?>" />

                    <!-- Accions -->
                    <div class="login__acciones">
                        <button type="submit" class="boton boton--marca">
                            Crear publicació
                        </button>
                        <button type="reset" class="boton">Netejar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>