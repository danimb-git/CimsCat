<?php
// processa_editar_excursio.php
session_start();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Location: /perfiladministrador.php?e=method'); exit; }
if (empty($_SESSION['user_id'])) { header('Location: /login.php?e=required'); exit; }
if (($_SESSION['rol'] ?? '') !== 'administrador') { header('Location: /perfil.php'); exit; }

require_once __DIR__ . '/../src/config/Database.php';

function clean(?string $s): ?string { if ($s===null) return null; $s=trim($s); return $s===''?null:$s; }

$id           = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$titol        = clean($_POST['titol'] ?? null);
$descripcio   = clean($_POST['descripcio'] ?? null);
$nom_cim      = clean($_POST['nom_cim'] ?? null);
$alcada_in    = $_POST['alcada'] ?? null;
$alcada       = is_numeric($alcada_in) ? (int)$alcada_in : null;
$comarca      = clean($_POST['comarca'] ?? null);
$dificultat   = clean($_POST['dificultat'] ?? null);
$dist_in      = $_POST['distancia'] ?? null;             
$distancia    = is_numeric($dist_in) ? (int)$dist_in : null;
$temps_ruta   = clean($_POST['temps_ruta'] ?? null);
$data         = clean($_POST['data'] ?? null);

// Valida bàsics
if (!$id || $id<=0)  { header('Location:/perfiladministrador.php?e=id'); exit; }
if (!$titol)         { header('Location:/editar_excursio.php?id='.$id.'&e=titol'); exit; }
$allowed = ['facil','mig','dificil'];
if ($dificultat && !in_array($dificultat,$allowed,true)) $dificultat = null;

// Normalitza data
if ($data) {
  $dt = DateTime::createFromFormat('Y-m-d', $data);
  if (!$dt) { try { $dt = new DateTime($data); } catch (\Throwable $e) { $dt = null; } }
  $data = $dt ? $dt->format('Y-m-d') : null;
}

$pdo = (new Database())->getConnection();

// 1) Obtenim les imatges actuals de la BD
$cur = $pdo->prepare("SELECT imatges FROM excursio WHERE id = ?");
$cur->execute([$id]);
$actual = $cur->fetch(PDO::FETCH_ASSOC);

$imatges_actuals_str = $actual['imatges'] ?? '';
$llista_imatges = [];

if ($imatges_actuals_str !== null && trim($imatges_actuals_str) !== '') {
    $llista_imatges = array_filter(
        array_map('trim', explode(',', $imatges_actuals_str))
    );
}

// Ruta de la carpeta d'uploads
$uploadDir = __DIR__ . '/uploads';

// 2) Eliminar imatges marcades al formulari
if (!empty($_POST['eliminar_imatges']) && is_array($_POST['eliminar_imatges'])) {
    foreach ($_POST['eliminar_imatges'] as $nom) {
        $nom = basename($nom); // seguretat
        $idx = array_search($nom, $llista_imatges, true);
        if ($idx !== false) {
            unset($llista_imatges[$idx]);

            $fitxer = $uploadDir . '/' . $nom;
            if (is_file($fitxer)) {
                @unlink($fitxer);
            }
        }
    }
}

// 3) Processar noves imatges pujades (si n'hi ha)
if (!empty($_FILES['imatges']) && is_array($_FILES['imatges']['name'])) {
    // Ens assegurem que la carpeta existeix
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $totalFiles = count($_FILES['imatges']['name']);
    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['imatges']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $tmpName  = $_FILES['imatges']['tmp_name'][$i];
        $origName = $_FILES['imatges']['name'][$i];

        if (!$tmpName || !is_uploaded_file($tmpName)) {
            continue;
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'], true)) {
            continue; // tipus no permès
        }

        $nouNom = uniqid('img_', true) . '.' . $ext;

        if (move_uploaded_file($tmpName, $uploadDir . '/' . $nouNom)) {
            $llista_imatges[] = $nouNom;
        }
    }
}

// 4) Tornem a muntar la cadena per guardar-la a la BD
$llista_imatges = array_values(array_unique($llista_imatges));
$imatges = null;
if (!empty($llista_imatges)) {
    $imatges = implode(',', $llista_imatges);
}

// UPDATE
$sql = "UPDATE excursio
        SET titol = :titol,
            descripcio = :descripcio,
            data = :data,
            temps_ruta = :temps_ruta,
            dificultat = :dificultat,
            distancia = :distancia,
            imatges = :imatges,
            cim_nom = :cim_nom,
            cim_alcada = :cim_alcada,
            cim_comarca = :cim_comarca
        WHERE id = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':titol',        $titol);
$stmt->bindValue(':descripcio',   $descripcio,  $descripcio===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':data',         $data,        $data===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':temps_ruta',   $temps_ruta,  $temps_ruta===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':dificultat',   $dificultat,  $dificultat===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':distancia',    $distancia,   $distancia===null ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindValue(':imatges',      $imatges,     $imatges===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':cim_nom',      $nom_cim,     $nom_cim===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':cim_alcada',   $alcada,      $alcada===null ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindValue(':cim_comarca',  $comarca,     $comarca===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':id',           $id,          PDO::PARAM_INT);
$stmt->execute();

header('Location: /perfiladministrador.php?ok=updated');
exit;
