<?php
// public/process_register.php
session_start();

// 1) Recoger y validar lo básico
$nom_usuari = trim($_POST['nom_usuari'] ?? '');
$nom        = trim($_POST['nom'] ?? '');
$cognom     = trim($_POST['cognom'] ?? '');
$mail       = trim($_POST['mail'] ?? '');
$password   = $_POST['password'] ?? '';
$password2  = $_POST['password2'] ?? '';

if ($password !== $password2) {
  header('Location: /register.php?e=pw_mismatch'); exit;
}

$edat = trim($_POST['edat'] ?? '');
$rol  = 'usuari';

if ($nom_usuari === '' || $nom === '' || $cognom === '' || $mail === '' || $password === '') {
  header('Location: /register.php?e=missing'); exit;
}

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
  header('Location: /register.php?e=badmail'); exit;
}

// ✅ Pujada d'avatar amb suport per ALTA QUALITAT
$fotoPath = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
  ];

  $tmp  = $_FILES['foto']['tmp_name'];
  
  // Validar MIME real
  $finfo = @finfo_open(FILEINFO_MIME_TYPE);
  $mime  = $finfo ? finfo_file($finfo, $tmp) : mime_content_type($tmp);
  if ($finfo) finfo_close($finfo);

  if (!isset($allowed[$mime])) {
    header('Location: /register.php?e=foto_tipo'); exit;
  }
  
  // ✅ AUGMENTAT A 10MB per avatars d'alta qualitat
  if ($_FILES['foto']['size'] > 10 * 1024 * 1024) { // 10MB
    header('Location: /register.php?e=foto_mida'); exit;
  }

  $ext = $allowed[$mime];
  $uploadDirFs = __DIR__ . '/uploads/avatars';
  if (!is_dir($uploadDirFs)) { 
    mkdir($uploadDirFs, 0777, true); 
  }

  $fileName = 'ava_' . bin2hex(random_bytes(8)) . '.' . $ext;
  $destFs   = $uploadDirFs . '/' . $fileName;

  if (!move_uploaded_file($tmp, $destFs)) {
    header('Location: /register.php?e=foto_move'); exit;
  }

  // ✅ Optimitzar avatar mantenint bona qualitat
  optimitzarAvatar($destFs, $mime);

  // Path relatiu per la BD
  $fotoPath = 'uploads/avatars/' . $fileName;
}

// 2) Conexión PDO
require_once __DIR__ . '/../src/config/Database.php';
$pdo = (new Database())->getConnection();

// 3) Comprobar que nom_usuari o mail no existen
$st = $pdo->prepare('SELECT id FROM usuari WHERE nom_usuari = ? OR mail = ? LIMIT 1');
$st->execute([$nom_usuari, $mail]);
if ($st->fetch()) {
  header('Location: /register.php?e=exists'); exit;
}

// 4) Hashear y guardar
$hash = password_hash($password, PASSWORD_DEFAULT);

$st = $pdo->prepare('INSERT INTO usuari (nom_usuari, nom, cognom, mail, contrasenya, edat, rol, foto)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$ok = $st->execute([
  $nom_usuari,
  $nom,
  $cognom,
  $mail,
  $hash,
  $edat !== '' ? (int)$edat : null,
  $rol,
  $fotoPath
]);

if (!$ok) {
  header('Location: /register.php?e=dberr'); exit;
}

// 5) Éxito → al login
header('Location: /login.php?e=registered');
exit;

/**
 * ✅ FUNCIÓ PER OPTIMITZAR AVATARS
 * - Redimensiona a màxim 800x800px (suficient per avatars)
 * - Manté qualitat alta (85-90%)
 */
function optimitzarAvatar($filepath, $mimeType) {
  // Carregar imatge
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
      return;
  }
  
  if (!$image) return;
  
  $width = imagesx($image);
  $height = imagesy($image);
  
  // Redimensionar a màxim 800x800 per avatars
  $maxSize = 800;
  
  if ($width > $maxSize || $height > $maxSize) {
    if ($width > $height) {
      $newWidth = $maxSize;
      $newHeight = intval($height * ($maxSize / $width));
    } else {
      $newHeight = $maxSize;
      $newWidth = intval($width * ($maxSize / $height));
    }
    
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    
    // Mantenir transparència
    if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
      imagealphablending($resized, false);
      imagesavealpha($resized, true);
    }
    
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Guardar amb bona qualitat
    switch ($mimeType) {
      case 'image/jpeg':
        imagejpeg($resized, $filepath, 85);
        break;
      case 'image/png':
        imagepng($resized, $filepath, 7);
        break;
      case 'image/webp':
        imagewebp($resized, $filepath, 85);
        break;
    }
    
    imagedestroy($resized);
  } else {
    // Només recomprimir
    switch ($mimeType) {
      case 'image/jpeg':
        imagejpeg($image, $filepath, 90);
        break;
      case 'image/png':
        imagepng($image, $filepath, 6);
        break;
      case 'image/webp':
        imagewebp($image, $filepath, 90);
        break;
    }
  }
  
  imagedestroy($image);
}
?>