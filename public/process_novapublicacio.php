<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: /register.php?e=login'); exit; }

require __DIR__ . '/../src/config/Database.php';

function redirect($to){ header("Location: $to"); exit; }

$uid = (int)$_SESSION['user_id'];

/* === 1) Inputs === */
$titol      = trim($_POST['titol'] ?? '');
$descripcio = trim($_POST['descripcio'] ?? '');
$data       = $_POST['data_publicacio'] ?? '';
$temps      = $_POST['temps_ruta'] ?? '';
$dificultat = $_POST['dificultat'] ?? '';
$distancia  = $_POST['distancia'] ?? '';

$nom_cim = trim($_POST['nom_cim'] ?? '');
$alcada  = (int)($_POST['alcada'] ?? 0);
$comarca = trim($_POST['comarca'] ?? '');

/* === 2) Validació bàsica === */
$errors = [];
if ($titol==='') $errors[]='titol';
if ($descripcio==='') $errors[]='descripcio';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$data)) $errors[]='data';
if (!preg_match('/^\d{2}:\d{2}$/',$temps)) $errors[]='temps';
if (!in_array($dificultat, ['facil','mig','dificil'], true)) $errors[]='dificultat';
if (!is_numeric($distancia)) $errors[]='distancia';
if ($nom_cim==='') $errors[]='nom_cim';
if ($alcada<=0) $errors[]='alcada';
if ($comarca==='') $errors[]='comarca';

if ($errors) { redirect('/novapublicacio.php?e=' . implode(',', $errors)); }

/* === 3) Pujada d’imatges (opcional) === */
$paths = [];
if (!empty($_FILES['imatges']['name'][0])) {
  if (count($_FILES['imatges']['name']) > 5) redirect('/novapublicacio.php?e=toomany');
  $uploadDir = __DIR__ . '/uploads';
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

  for ($i=0; $i<count($_FILES['imatges']['name']); $i++) {
    $tmp = $_FILES['imatges']['tmp_name'][$i];
    if (!is_uploaded_file($tmp)) continue;
    $mime = mime_content_type($tmp);
    if (!in_array($mime, ['image/jpeg','image/png'], true)) redirect('/novapublicacio.php?e=img');

    $ext  = pathinfo($_FILES['imatges']['name'][$i], PATHINFO_EXTENSION);
    $name = uniqid('img_', true).".".$ext;
    $dest = $uploadDir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) redirect('/novapublicacio.php?e=upload');

    $paths[] = 'uploads/' . $name; // ruta pública
  }
}
$imatges = $paths ? implode(',', $paths) : null;

/* === 4) Inserció a EXCURSIO (sense taula CIM) === */
try {
  $pdo = (new Database())->getConnection();

  // si a la BD 'distancia' és INT, fem cast segur:
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

  redirect('/publicacio.html?s=publicada');
} catch (Throwable $e) {
  // Debug opcional temporal:
  // ini_set('display_errors',1); error_reporting(E_ALL); die($e->getMessage());
  redirect('/novapublicacio.php?e=bd');
}
