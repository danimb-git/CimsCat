<?php
require_once __DIR__ . '/controllers/ExcursioController.php';

if (isset($_GET['id'])) {
    ExcursioController::veure($_GET['id']);
} elseif (isset($_GET['q'])) {
    ExcursioController::cercar($_GET['q']);
} else {
    ExcursioController::llistar();
}
