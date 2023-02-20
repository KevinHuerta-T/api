<?php

// Cargar archivo de configuración de la base de datos
require_once '../config/database.php';

// Cargar modelo de usuario
require_once '../models/user.php';

// Cargar modelo de publicación
require_once '../models/post.php';

// Cargar middleware de autenticación JWT
require_once '../middleware/jwt.php';

// Cargar controladores de autenticación
require_once '../auth/login.php';
require_once '../auth/logout.php';

// Cargar controladores de publicaciones
require_once '../posts/create.php';
require_once '../posts/read.php';
require_once '../posts/update.php';
require_once '../posts/delete.php';

// Configurar cabeceras HTTP
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Obtener método HTTP y ruta
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

// Verificar si la ruta requiere autenticación JWT
$require_auth = true;
if ($require_auth) {
    try {
        $jwt = new JwtMiddleware();
        $user = $jwt->verifyToken();
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array('message' => 'Error de autenticación: ' . $e->getMessage()));
        exit;
    }
}

// Enrutamiento
switch ($request[0]) {
    case 'auth':
        if ($method === 'POST') {
            login();
        } else if ($method === 'DELETE') {
            logout();
        } else {
            http_response_code(404);
            echo json_encode(array('message' => 'Ruta no encontrada'));
        }
        break;
    case 'posts':
        switch ($method) {
            case 'GET':
                read();
                break;
            case 'POST':
                create();
                break;
            case 'PUT':
                update();
                break;
            case 'DELETE':
                delete();
                break;
            default:
                http_response_code(404);
                echo json_encode(array('message' => 'Ruta no encontrada'));
                break;
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(array('message' => 'Ruta no encontrada'));
        break;
}
