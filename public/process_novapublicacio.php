<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: /login.php?e=required'); exit; }

require_once __DIR__ . '/../src/config/Database.php';
function redirect($to){ header("Location: $to"); exit; }

$uid = (int)$_SESSION['user_id'];

/* === 1) Inputs === */
$titol      = trim($_POST['titol'] ?? '');
$descripcio = trim($_POST['descripcio'] ?? '');
$data       = $_POST['data'] ?? '';            // <-- nombre alineado con el form
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

  $uploadDir = __DIR__ . '/uploads'; // /public/uploads
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

  // Límite de tamaño (ej. 5MB por imagen)
  $maxSize = 5 * 1024 * 1024;
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

  for ($i=0; $i<count($_FILES['imatges']['name']); $i++) {
    if ($_FILES['imatges']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
    if ($_FILES['imatges']['error'][$i] !== UPLOAD_ERR_OK) redirect('/novapublicacio.php?e=upload');

    $tmp  = $_FILES['imatges']['tmp_name'][$i];
    $size = (int)$_FILES['imatges']['size'][$i];
    if ($size <= 0 || $size > $maxSize) redirect('/novapublicacio.php?e=imgsize');

    // MIME real -> extensión segura
    $finfo = @finfo_open(FILEINFO_MIME_TYPE);
    $mime  = $finfo ? finfo_file($finfo, $tmp) : mime_content_type($tmp);
    if ($finfo) finfo_close($finfo);

    if (!isset($allowed[$mime])) redirect('/novapublicacio.php?e=imgtype');

    $ext  = $allowed[$mime];
    $name = uniqid('img_', true) . '.' . $ext;
    $dest = $uploadDir . '/' . $name;

    if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $dest)) {
      redirect('/novapublicacio.php?e=upload');
    }

    // ruta pública relativa
    $paths[] = 'uploads/' . $name;
  }
}
$imatges = $paths ? implode(',', $paths) : null;

/* === 4) Inserció a EXCURSIO (sense taula CIM) === */
try {
  $pdo = Database::getConnection();

  $dist = (int)$distancia; // BD: INT

  $sql = "INSERT INTO excursio
            (titol, descripcio, data, temps_ruta, dificultat, imatges, distancia,
             cim_nom, cim_alcada, cim_comarca, id_usuari)
          VALUES (?,?,?,?,?,?,?,?,?,?,?)";

  $st = $pdo->prepare($sql);
  $st->execute([
    $titol, $descripcio, $data, $temps, $dificultat, $imatges, $dist,
    $nom_cim, $alcada, $comarca, $uid
  ]);

  // === REDIRECCIÓN A LA FICHA ===
  $id = (int)$pdo->lastInsertId();
  if ($id > 0) {
    header('Location: /publicacio.php?id=' . $id);
    exit;
  }

  // Fallback si no se obtuvo el ID (muy raro)
  redirect('/perfil.php?ok=created');

} catch (Throwable $e) {
  // Log opcional: error_log($e->getMessage());
  redirect('/novapublicacio.php?e=bd');
}
?>