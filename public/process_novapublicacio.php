<?php
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: /login.php?e=required'); exit;
}

require_once __DIR__ . '/../src/config/Database.php';
function redirect($to){ header("Location: $to"); exit; }

$uid = (int)$_SESSION['user_id'];

/* === 1) Inputs === */
$titol      = trim($_POST['titol'] ?? '');
$descripcio = trim($_POST['descripcio'] ?? '');
$data       = $_POST['data'] ?? '';
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

/* === 3) Pujada d'imatges amb suport per ALTA QUALITAT === */
$paths = [];
if (!empty($_FILES['imatges']['name'][0])) {
  if (count($_FILES['imatges']['name']) > 5) redirect('/novapublicacio.php?e=toomany');

  $uploadDir = __DIR__ . '/uploads'; // /public/uploads
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

  // ✅ AUGMENTAT A 50MB per imatge d'alta qualitat
  $maxSize = 50 * 1024 * 1024; // 50MB
  
  // ✅ Afegit suport per WebP (millor compressió)
  $allowed = [
    'image/jpeg' => 'jpg', 
    'image/png'  => 'png',
    'image/webp' => 'webp'
  ];

  for ($i=0; $i<count($_FILES['imatges']['name']); $i++) {
    if ($_FILES['imatges']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
    
    // Gestionar errors específics
    if ($_FILES['imatges']['error'][$i] !== UPLOAD_ERR_OK) {
      $errorMsg = 'upload';
      switch ($_FILES['imatges']['error'][$i]) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          $errorMsg = 'imgsize';
          break;
        case UPLOAD_ERR_PARTIAL:
          $errorMsg = 'partial';
          break;
      }
      redirect('/novapublicacio.php?e=' . $errorMsg);
    }

    $tmp  = $_FILES['imatges']['tmp_name'][$i];
    $size = (int)$_FILES['imatges']['size'][$i];
    
    if ($size <= 0 || $size > $maxSize) {
      redirect('/novapublicacio.php?e=imgsize');
    }

    // MIME real -> extensió segura
    $finfo = @finfo_open(FILEINFO_MIME_TYPE);
    $mime  = $finfo ? finfo_file($finfo, $tmp) : mime_content_type($tmp);
    if ($finfo) finfo_close($finfo);

    if (!isset($allowed[$mime])) {
      redirect('/novapublicacio.php?e=imgtype');
    }

    $ext  = $allowed[$mime];
    $name = uniqid('img_', true) . '.' . $ext;
    $dest = $uploadDir . '/' . $name;

    if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $dest)) {
      redirect('/novapublicacio.php?e=upload');
    }

    // ✅ OPCIONAL: Optimitzar la imatge mantenint alta qualitat
    optimitzarImatgeAltaQualitat($dest, $mime);

    // ruta pública relativa
    $paths[] = 'uploads/' . $name;
  }
}
$imatges = $paths ? implode(',', $paths) : null;

/* === 4) Inserció a EXCURSIO === */
try {
  $pdo = (new Database())->getConnection();

  $dist = (int)$distancia;

  $sql = "INSERT INTO excursio
            (titol, descripcio, data, temps_ruta, dificultat, imatges, distancia,
             cim_nom, cim_alcada, cim_comarca, id_usuari)
          VALUES (?,?,?,?,?,?,?,?,?,?,?)";

  $st = $pdo->prepare($sql);
  $st->execute([
    $titol, $descripcio, $data, $temps, $dificultat, $imatges, $dist,
    $nom_cim, $alcada, $comarca, $uid
  ]);

  $id = (int)$pdo->lastInsertId();
  if ($id > 0) {
    header('Location: /publicacio.php?id=' . $id);
    exit;
  }

  redirect('/perfil.php?ok=created');

} catch (Throwable $e) {
  error_log($e->getMessage());
  redirect('/novapublicacio.php?e=bd');
}

/**
 * ✅ FUNCIÓ PER OPTIMITZAR IMATGES MANTENINT ALTA QUALITAT
 * - No redimensiona si és menor de 4000px
 * - Si és més gran, redimensiona mantenint aspect ratio
 * - Comprimeix amb qualitat 90-95% (molt alta)
 */
function optimitzarImatgeAltaQualitat($filepath, $mimeType) {
  // Carregar la imatge segons el tipus
  switch ($mimeType) {
    case 'image/jpeg':
      $image = @imagecreatefromjpeg($filepath);
      break;
    case 'image/png':
      $image = @imagecreatefrompng($filepath);
      break;
    case 'image/webp':
      $image = @imagecreatefromwebp($filepath);
      break;
    default:
      return; // Tipus no suportat
  }
  
  if (!$image) return; // Error en carregar
  
  $width = imagesx($image);
  $height = imagesy($image);
  
  // Només redimensionar si és MOLT gran (> 4000px)
  $maxDimension = 4000;
  
  if ($width > $maxDimension || $height > $maxDimension) {
    // Calcular noves dimensions mantenint aspect ratio
    if ($width > $height) {
      $newWidth = $maxDimension;
      $newHeight = intval($height * ($maxDimension / $width));
    } else {
      $newHeight = $maxDimension;
      $newWidth = intval($width * ($maxDimension / $height));
    }
    
    // Crear imatge redimensionada amb alta qualitat
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    
    // Mantenir transparència per PNG i WebP
    if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
      imagealphablending($resized, false);
      imagesavealpha($resized, true);
      $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
      imagefill($resized, 0, 0, $transparent);
    }
    
    // Redimensionar amb alta qualitat
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Guardar amb qualitat 90-95%
    switch ($mimeType) {
      case 'image/jpeg':
        imagejpeg($resized, $filepath, 90); // Qualitat 90%
        break;
      case 'image/png':
        imagepng($resized, $filepath, 7); // Compressió 7 (0=no compressió, 9=màxima)
        break;
      case 'image/webp':
        imagewebp($resized, $filepath, 90); // Qualitat 90%
        break;
    }
    
    imagedestroy($resized);
  } else {
    // NO redimensionar, només recomprimir amb qualitat màxima
    switch ($mimeType) {
      case 'image/jpeg':
        imagejpeg($image, $filepath, 95); // Qualitat 95%
        break;
      case 'image/png':
        imagepng($image, $filepath, 5); // Compressió mínima
        break;
      case 'image/webp':
        imagewebp($image, $filepath, 95); // Qualitat 95%
        break;
    }
  }
  
  imagedestroy($image);
}
?>