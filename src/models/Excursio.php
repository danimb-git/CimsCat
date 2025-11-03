<?php
require_once __DIR__ . '/../config/db.php';

class Excursio {
    // Obtenir totes les excursions
    public static function getAll() {
        $pdo = get_pdo();
        $stmt = $pdo->query("SELECT * FROM excursions ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir una excursió pel seu ID
    public static function getById($id) {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT * FROM excursions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar excursions pel nom o la comarca
    public static function search($term) {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT * FROM excursions WHERE nom LIKE ? OR comarca LIKE ?");
        $stmt->execute(["%$term%", "%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
