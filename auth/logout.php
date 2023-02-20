<?php
function logout(){ 
    // Archivo: auth/logout.php
// Descripción: endpoint para cerrar sesión

// Headers necesarios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Verificar que se haya enviado un token JWT
$jwt = null;
if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
    $auth_header = $_SERVER["HTTP_AUTHORIZATION"];
    $jwt = explode(" ", $auth_header)[1];
}

if ($jwt) {
    // Eliminar el token JWT del usuario
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $user->jwt = $jwt;
    $user->logout();

    // Enviar mensaje de éxito
    http_response_code(200);
    echo json_encode(array("message" => "Sesión cerrada correctamente."));
} else {
    // Si no se envió un token JWT, enviar mensaje de error
    http_response_code(401);
    echo json_encode(array("message" => "Acceso denegado."));
}
}
?>
