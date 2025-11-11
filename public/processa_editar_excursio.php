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
$dist_in      = $_POST['distancia'] ?? null;             // BD: INT
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

// Obté imatges actuals per mantenir-les si no hi ha pujada nova
$pdo = (new Database())->getConnection();
$cur = $pdo->prepare("SELECT imatges FROM excursio WHERE id = ?");
$cur->execute([$id]);
$actual = $cur->fetch(PDO::FETCH_ASSOC);
$imatges_actuals = $actual['imatges'] ?? null;

// (Opcional) gestionar upload d'imatges aquí. Si no canvies, es manté el valor existent.
$imatges = $imatges_actuals;

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
