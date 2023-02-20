<?php

function create(){ 
    // headers requeridos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// incluir la base de datos y los objetos Post y JWT
require_once '../config/database.php';
require_once '../models/post.php';
require_once '../middleware/jwt.php';

// instanciar objetos necesarios
$database = new Database();
$db = $database->getConnection();
$post = new Post($db);
$jwt = new JwtMiddleware();

// obtener los datos enviados
$data = json_decode(file_get_contents("php://input"));

// obtener el token del encabezado de autorización
$jwt->getTokenFromHeaders();

// validar el token
if (!$jwt->validateToken()) {
    http_response_code(401);
    echo json_encode(array("message" => "Acceso denegado."));
    exit;
}

// obtener el rol del usuario desde el token
$decoded = $jwt->decodeToken();
$rol = $decoded->data->rol;

// validar que el rol del usuario tenga permisos para agregar publicaciones
if ($rol != 'rol_medio_alto' && $rol != 'rol_alto_medio' && $rol != 'rol_alto') {
    http_response_code(401);
    echo json_encode(array("message" => "Acceso denegado. Rol de usuario no tiene permisos para agregar publicaciones."));
    exit;
}

// validar que se hayan proporcionado los datos necesarios
if (
    !empty($data->title) &&
    !empty($data->description)
) {
    // asignar los valores
    $post->title = $data->title;
    $post->description = $data->description;
    $post->created_at = date('Y-m-d H:i:s');
    $post->user_id = $decoded->data->id;

    // crear la publicación
    if ($post->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Publicación creada correctamente."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "No se pudo crear la publicación."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "No se pudieron crear la publicación. Los datos están incompletos."));
}
}
?>