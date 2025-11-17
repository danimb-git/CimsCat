<?php
// editar_excursio.php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: /login.php?e=required'); exit; }
if (($_SESSION['rol'] ?? '') !== 'administrador') { header('Location: /perfil.php'); exit; }

require_once __DIR__ . '/../src/config/Database.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location:/perfiladministrador.php?e=id'); exit; }

$pdo = (new Database())->getConnection();
$sql = "SELECT id, titol, descripcio, data, temps_ruta, dificultat, imatges, distancia,
               cim_nom, cim_alcada, cim_comarca, id_cim
        FROM excursio
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$ex = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ex) { header('Location:/perfiladministrador.php?e=notfound'); exit; }

// DATA -> Y-m-d
$data_value = '';
if (!empty($ex['data'])) {
  $dt = DateTime::createFromFormat('Y-m-d', $ex['data']);
  if (!$dt) { try { $dt = new DateTime($ex['data']); } catch (\Throwable $e) { $dt = null; } }
  if ($dt) $data_value = $dt->format('Y-m-d');
}

// TEMPS -> HH:MM
$time_value = '';
if (!empty($ex['temps_ruta'])) {
  $raw = trim($ex['temps_ruta']);
  if (preg_match('/^\d{1,2}:\d{2}$/', $raw)) {
    $time_value = $raw;
  } else {
    $h=0; $m=0;
    if (preg_match('/(\d+)\s*h/i', $raw, $mh)) $h=(int)$mh[1];
    if (preg_match('/(\d+)\s*m/i', $raw, $mm)) $m=(int)$mm[1];
    if ($h===0 && $m===0 && ctype_digit($raw)) $h=(int)$raw;
    if ($h===0 && $m>=60) { $h=intdiv($m,60); $m=$m%60; }
    $time_value = sprintf('%02d:%02d', min($h,23), $m);
  }
}

$difs = ['facil'=>'Fàcil','mig'=>'Mig','dificil'=>'Difícil'];
$imatges_actuals = trim((string)($ex['imatges'] ?? ''));
$llista_imatges = [];
if ($imatges_actuals !== '') {
  $llista_imatges = array_filter(array_map('trim', explode(',', $imatges_actuals)));
}


// Prefill dels camps del cim
$nom_cim = (string)($ex['cim_nom'] ?? '');
$alcada  = (string)($ex['cim_alcada'] ?? '');
$comarca = (string)($ex['cim_comarca'] ?? '');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Editar publicació</title>
  <link rel="stylesheet" href="css/01-base.css" />
  <link rel="stylesheet" href="css/02-layout.css" />
  <link rel="stylesheet" href="css/03-componentes.css" />
  <link rel="stylesheet" href="css/04-paginas.css" />
</head>
<body>
  <section class="seccion login">
    <div class="contenedor">
      <h1 class="seccion__titulo login__titulo">Editar publicació</h1>

      <div class="login__card">
        <form action="/processa_editar_excursio.php" method="post" class="login__form" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="id" value="<?= (int)$ex['id'] ?>">

          <!-- Títol -->
          <div class="login__grupo">
            <label for="titol" class="login__label">Títol de la publicació *</label>
            <input type="text" id="titol" name="titol" class="login__input" autocomplete="off" required maxlength="120"
                   value="<?= e($ex['titol'] ?? '') ?>" />
          </div>

          <!-- Descripció -->
          <div class="login__grupo">
            <label for="descripcio" class="login__label">Descripció *</label>
            <textarea id="descripcio" name="descripcio" class="login__textarea" rows="6" required><?= e($ex['descripcio'] ?? '') ?></textarea>
          </div>

          <!-- Nom del cim -->
          <div class="login__grupo">
            <label for="nom_cim" class="login__label">Nom del cim *</label>
            <input type="text" id="nom_cim" name="nom_cim" class="login__input" autocomplete="off" required
                   value="<?= e($nom_cim) ?>" />
          </div>

          <!-- Alçada (m) -->
          <div class="login__grupo">
            <label for="alcada" class="login__label">Alçada (m) *</label>
            <input type="number" id="alcada" name="alcada" class="login__input" min="1" max="6000" step="1" required
                   value="<?= e($alcada) ?>" />
          </div>

          <!-- Comarca -->
          <div class="login__grupo">
            <label for="comarca" class="login__label">Comarca *</label>
            <select id="comarca" name="comarca" class="login__select" required data-current="<?= e($comarca) ?>"></select>
            <script>
              const COMARQUES = ["Alt Camp","Alt Empordà","Alt Penedès","Alt Urgell","Alta Ribagorça","Anoia","Bages","Baix Camp","Baix Ebre","Baix Empordà","Baix Llobregat","Baix Penedès","Barcelonès","Berguedà","Cerdanya","Conca de Barberà","Garraf","Garrigues","Garrotxa","Gironès","Maresme","Moianès","Montsià","Noguera","Osona","Pallars Jussà","Pallars Sobirà","Pla d'Urgell","Pla de l'Estany","Priorat","Ribera d'Ebre","Ripollès","Segarra","Segrià","Selva","Solsonès","Tarragonès","Terra Alta","Urgell","Val d'Aran","Vallès Occidental","Vallès Oriental"];
              const s = document.getElementById("comarca");
              const cur = s.getAttribute('data-current') || '';
              s.innerHTML =
                "<option disabled>— Selecciona la comarca —</option>" +
                COMARQUES.map((c) => `<option ${c===cur?'selected':''}>${c}</option>`).join("") +
                '<option value="altres" '+(cur==='altres'?'selected':'')+'>Altres…</option>';
              if (!cur) s.firstElementChild.setAttribute('selected','selected');
            </script>
          </div>

          <!-- Dificultat -->
          <div class="login__grupo">
            <label for="dificultat" class="login__label">Dificultat *</label>
            <select id="dificultat" name="dificultat" class="login__select" required>
              <option value="" disabled>— Selecciona —</option>
              <?php foreach ($difs as $val=>$label): ?>
                <option value="<?= e($val) ?>" <?= ($ex['dificultat'] ?? '') === $val ? 'selected':'' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Distància (km) — a la BD és INT -->
          <div class="login__grupo">
            <label for="distancia" class="login__label">Distància (km) *</label>
            <input type="number" id="distancia" name="distancia" class="login__input" min="0" step="1" required
                   value="<?= e((string)($ex['distancia'] ?? '')) ?>" />
          </div>

          <!-- Duració -->
          <div class="login__grupo">
            <label for="temps_ruta" class="login__label">Duració (hores:minuts) *</label>
            <input type="time" id="temps_ruta" name="temps_ruta" class="login__input" required
                   value="<?= e($time_value) ?>" />
          </div>

          <!-- Data -->
          <div class="login__grupo">
            <label for="data_publicacio" class="login__label">Data *</label>
            <input type="date" id="data_publicacio" name="data" class="login__input" required
                   value="<?= e($data_value) ?>" />
          </div>

          <!-- Imatges -->
          <div class="login__grupo">
            <label for="imatges" class="login__label">Imatges (JPG/PNG, fins a 5)</label>
            <input
              type="file"
              id="imatges"
              name="imatges[]"
              class="login__input"
              accept=".jpg,.jpeg,.png"
              multiple
            />

            <?php if (!empty($llista_imatges)): ?>
              <p class="login__label" style="margin-top: 0.5rem;">Imatges actuals</p>
              <ul class="imatges-actuals">
                <?php foreach ($llista_imatges as $img): ?>
                  <li class="imatges-actuals__item">
                    <!-- Si vols, pots posar també una miniatura amb <img> -->
                    <!-- <img src="/uploads/<?= e($img) ?>" alt="" style="max-width: 80px; display:block; margin-bottom:4px;"> -->
                    <span><?= e($img) ?></span>
                    <label style="margin-left: .5rem; font-size: .9rem;">
                      <input
                        type="checkbox"
                        name="eliminar_imatges[]"
                        value="<?= e($img) ?>"
                      >
                      Eliminar
                    </label>
                  </li>
                <?php endforeach; ?>
              </ul>
              <small class="login__ayuda">
                Si marques “Eliminar” i no puges cap imatge nova, la publicació es pot quedar sense fotos.
              </small>
            <?php else: ?>
              <small class="login__ayuda">Ara mateix aquesta publicació no té cap imatge guardada.</small>
            <?php endif; ?>
          </div>


          <!-- Accions -->
          <div class="login__acciones">
            <button type="submit" class="boton boton--marca">Desar canvis</button>
            <a class="boton" href="/perfiladministrador.php">Cancel·lar</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
