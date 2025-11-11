<?php
session_start();
require_once __DIR__ . '/../../src/models/Comentari.php';

/**
 * Classe ComentariController
 * Controlador per gestionar les accions relacionades amb els comentaris
 */
class ComentariController {
    
    /**
     * CREATE - Crear un nou comentari
     * Retorna JSON amb el resultat
     */
    public static function create() {
        header('Content-Type: application/json; charset=utf-8');
        
        // Comprovar que l'usuari està autenticat
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió per comentar'
            ]);
            return;
        }

        // Comprovar que s'han rebut les dades necessàries
        if (!isset($_POST['id_excursio']) || !isset($_POST['contingut'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Dades incompletes'
            ]);
            return;
        }

        // Validar el contingut
        $validacio = Comentari::validate($_POST['contingut']);
        if (!$validacio['valid']) {
            echo json_encode([
                'success' => false,
                'error' => implode(', ', $validacio['errors'])
            ]);
            return;
        }

        // Crear el comentari
        $comentari = new Comentari();
        $comentari->contingut = $_POST['contingut'];
        $comentari->id_excursio = intval($_POST['id_excursio']);
        $comentari->id_usuari = $_SESSION['user_id'];

        $comentari_id = $comentari->create();

        if ($comentari_id) {
            // Obtenir el comentari creat amb tota la info
            $comentari_creat = $comentari->getById($comentari_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Comentari afegit correctament',
                'comentari' => $comentari_creat
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Error al crear el comentari'
            ]);
        }
    }

    /**
     * READ - Obtenir tots els comentaris d'una excursió
     * Retorna JSON amb l'array de comentaris
     */
    public static function getByExcursio() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_GET['id_excursio'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID d\'excursió no proporcionat'
            ]);
            return;
        }

        $comentari = new Comentari();
        $comentaris = $comentari->getByExcursio(intval($_GET['id_excursio']));

        echo json_encode([
            'success' => true,
            'comentaris' => $comentaris,
            'total' => count($comentaris)
        ]);
    }

    /**
     * UPDATE - Actualitzar un comentari
     * Retorna JSON amb el resultat
     */
    public static function update() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió'
            ]);
            return;
        }

        if (!isset($_POST['id']) || !isset($_POST['contingut'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Dades incompletes'
            ]);
            return;
        }

        // Validar el contingut
        $validacio = Comentari::validate($_POST['contingut']);
        if (!$validacio['valid']) {
            echo json_encode([
                'success' => false,
                'error' => implode(', ', $validacio['errors'])
            ]);
            return;
        }

        $comentari = new Comentari();
        $comentari->id = intval($_POST['id']);
        $comentari->contingut = $_POST['contingut'];
        $comentari->id_usuari = $_SESSION['user_id'];

        if ($comentari->update()) {
            // Obtenir el comentari actualitzat
            $comentari_actualitzat = $comentari->getById($comentari->id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Comentari actualitzat correctament',
                'comentari' => $comentari_actualitzat
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Error al actualitzar el comentari o no tens permís'
            ]);
        }
    }

    /**
     * DELETE - Eliminar un comentari
     * Retorna JSON amb el resultat
     */
    public static function delete() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió'
            ]);
            return;
        }

        if (!isset($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID del comentari no proporcionat'
            ]);
            return;
        }

        $comentari = new Comentari();
        $id = intval($_POST['id']);
        $id_usuari = $_SESSION['user_id'];

        // Si és administrador, pot eliminar qualsevol comentari
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador') {
            $success = $comentari->deleteByAdmin($id);
        } else {
            // Si és usuari normal, només pot eliminar els seus
            $success = $comentari->delete($id, $id_usuari);
        }

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Comentari eliminat correctament'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Error al eliminar el comentari o no tens permís'
            ]);
        }
    }

    /**
     * COUNT - Comptar comentaris d'una excursió
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

        $comentari = new Comentari();
        $count = $comentari->countByExcursio(intval($_GET['id_excursio']));

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * GET_USER_COMMENTS - Obtenir tots els comentaris d'un usuari
     * Retorna JSON amb l'array de comentaris
     */
    public static function getUserComments() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Has d\'iniciar sessió'
            ]);
            return;
        }

        $comentari = new Comentari();
        $comentaris = $comentari->getByUsuari($_SESSION['user_id']);

        echo json_encode([
            'success' => true,
            'comentaris' => $comentaris,
            'total' => count($comentaris)
        ]);
    }
}

// Processar la petició segons l'acció i el mètode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'create':
            ComentariController::create();
            break;
        case 'update':
            ComentariController::update();
            break;
        case 'delete':
            ComentariController::delete();
            break;
        default:
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'Acció no reconeguda'
            ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'list':
            ComentariController::getByExcursio();
            break;
        case 'count':
            ComentariController::count();
            break;
        case 'user_comments':
            ComentariController::getUserComments();
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