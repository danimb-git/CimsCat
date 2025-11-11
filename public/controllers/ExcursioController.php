<?php
require_once __DIR__ . '/../../src/models/Excursio.php';

class ExcursioController {
    public static function llistar() {
        $excursions = Excursio::getAll();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($excursions);
    }

    public static function veure($id) {
        $excursio = Excursio::getById($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($excursio);
    }

    public static function cercar($terme) {
        $resultats = Excursio::search($terme);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultats);
    }
}
