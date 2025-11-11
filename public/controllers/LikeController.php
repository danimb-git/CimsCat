<?php
session_start();
require_once __DIR__ . '/../../src/models/Like.php';

/**
 * Classe LikeController
 * Controlador per gestionar les accions relacionades amb els likes
 */
class LikeController {
    
    /**
     * TOGGLE LIKE - Afegir o eliminar like d'una excursió
     * Retorna JSON amb el resultat
     */
    public static function toggle() {
        // Configurar header per retornar JSON
        header('Content-Type: application/json; charset=utf-8');
        
        // Comprovar que l'usuari està autenticat
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió per fer like'
            ]);
            return;
        }

        // Comprovar que s'ha rebut l'ID de l'excursió
        if (!isset($_POST['id_excursio'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID d\'excursió no proporcionat'
            ]);
            return;
        }

        // Crear objecte Like
        $like = new Like();
        $like->id_excursio = intval($_POST['id_excursio']);
        $like->id_usuari = $_SESSION['user_id'];

        // Executar toggle (afegir o eliminar)
        $result = $like->toggle();

        // Retornar resultat
        echo json_encode($result);
    }

    /**
     * COUNT - Obtenir el número de likes d'una excursió
     * Retorna JSON amb el total
     */
    public static function count() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_GET['id_excursio'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID d\'excursió no proporcionat'
            ]);
            return;
        }

        $like = new Like();
        $count = $like->countByExcursio(intval($_GET['id_excursio']));

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * CHECK - Comprovar si l'usuari actual ha fet like a una excursió
     * Retorna JSON amb liked: true/false
     */
    public static function check() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id']) || !isset($_GET['id_excursio'])) {
            echo json_encode([
                'success' => true,
                'liked' => false
            ]);
            return;
        }

        $like = new Like();
        $like->id_excursio = intval($_GET['id_excursio']);
        $like->id_usuari = $_SESSION['user_id'];

        echo json_encode([
            'success' => true,
            'liked' => $like->exists()
        ]);
    }

    /**
     * GET_USER_LIKES - Obtenir totes les excursions que li agraden a un usuari
     * Retorna JSON amb l'array d'excursions
     */
    public static function getUserLikes() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió'
            ]);
            return;
        }

        $like = new Like();
        $likes = $like->getByUsuari($_SESSION['user_id']);

        echo json_encode([
            'success' => true,
            'likes' => $likes
        ]);
    }
}

// Processar la petició segons l'acció
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST: Toggle like
    $action = $_POST['action'] ?? 'toggle';
    
    switch($action) {
        case 'toggle':
            LikeController::toggle();
            break;
        default:
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'Acció no reconeguda'
            ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET: Obtenir informació
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'count':
            LikeController::count();
            break;
        case 'check':
            LikeController::check();
            break;
        case 'user_likes':
            LikeController::getUserLikes();
            break;
        default:
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'Acció no reconeguda'
            ]);
    }
}
?>