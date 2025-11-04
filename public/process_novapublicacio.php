<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: /register.php?e=login'); exit; }

require __DIR__ . '/../src/config/Database.php';

function redirect($to){ header("Location: $to"); exit; }
$uid = (int)$_SESSION['user_id'];

// 1) Inputs
$titol      = trim($_POST['titol'] ?? '');
$descripcio = trim($_POST['descripcio'] ?? '');
$data       = $_POST['data_publicacio'] ?? '';
$temps      = $_POST['temps_ruta'] ?? '';
$dificultat = $_POST['dificultat'] ?? '';
$distancia  = $_POST['distancia'] ?? '';

// Camps “cim” (ara es guarden a la mateixa taula EXCURSIO)
$nom_cim = trim($_POST['nom_cim'] ?? '');
$alcada  = (int)($_POST['alcada'] ?? 0);
$comarca = trim($_POST['comarca'] ?? '');

// 2) Validació bàsica
if ($titol==='' || $descripcio==='' || $nom_cim==='' || $alcada<=0 || $comarca==='') {
  redirect('/novapublicacio.php?e=validacio');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$data))   redirect('/novapublicacio.php?e=data');
if (!preg_match('/^\d{2}:\d{2}$/',$temps))        redirect('/novapublicacio.php?e=temps');
if (!in_array($dificultat, ['facil','mig','dificil'], true)) redirect('/novapublicacio.php?e=dificultat');
if (!is_numeric($distancia)) redirect('/novapublicacio.php?e=distancia');

// 3) Pujada d’imatges (opcional)
$paths = [];
if (!empty($_FILES['imatges']['name'][0])) {
  if (count($_FILES['imatges']['name'])>5) redirect('/novapublicacio.php?e=toomany');
  $uploadDir = __DIR__ . '/uploads';
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

  for ($i=0; $i<count($_FILES['imatges']['name']); $i++) {
    $tmp  = $_FILES['imatges']['tmp_name'][$i];
    $name = $_FILES['imatges']['name'][$i];
    if (!is_uploaded_file($tmp)) continue;
    $mime = mime_content_type($tmp);
    if (!in_array($mime, ['image/jpeg','image/png'], true)) redirect('/novapublicacio.php?e=img');

    $ext  = pathinfo($name, PATHINFO_EXTENSION);
    $destName = uniqid('img_', true).".".$ext;
    $destPath = $uploadDir.'/'.$destName;
    move_uploaded_file($tmp, $destPath);
    $paths[] = 'uploads/'.$destName;
  }
}
$imatges = $paths ? implode(',', $paths) : null;

// 4) Inserció directa a EXCURSIO (sense taula CIM)
try {
  $pdo = Database::getConnection();

  // Nota: si la columna distancia és INT a la BD, forcem enter:
  $dist = (int)floor((float)$distancia);

  $sql = "INSERT INTO excursio
          (titol, descripcio, data, temps_ruta, dificultat, imatges, distancia,
           cim_nom, cim_alcada, cim_comarca, id_usuari)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)";

  $st = $pdo->prepare($sql);
  $st->execute([
    $titol, $descripcio, $data, $temps, $dificultat, $imatges, $dist,
    $nom_cim, $alcada, $comarca, $uid
  ]);


  redirect('/index.html?s=publicada');
} catch (Throwable $e) {
  redirect('/novapublicacio.php?e=bd');
}
