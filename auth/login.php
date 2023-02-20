<?php
function login(){ 
    // Archivo: auth/login.php
// Descripción: endpoint para la autenticación de usuarios

// Headers necesarios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir archivos de configuración y modelo de usuario
require_once '../config/database.php';
require_once '../models/user.php';

// Obtener datos enviados en la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que se hayan enviado datos
if (
    !empty($data->email) &&
    !empty($data->password)
) {
    // Inicializar la base de datos y crear una instancia del modelo de usuario
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    // Establecer las propiedades del modelo con los datos enviados
    $user->email = $data->email;
    $user->password = $data->password;

    // Intentar autenticar al usuario
    if ($user->login($data->email, $data->password, $db)) {
        // Si las credenciales son correctas, generar un token JWT
        $user_info = array(
            "id" => $user->id,
            "name" => $user->first_name,
            "email" => $user->email,
            "role" => $user->role
        );
        $jwt = $user->generateJWT($user_info);

        // Crear objeto de respuesta
        http_response_code(200);
        echo json_encode(array(
            "message" => "Autenticación exitosa.",
            "jwt" => $jwt
        ));
    } else {
        // Si las credenciales no son correctas, enviar mensaje de error
        http_response_code(401);
        echo json_encode(array("message" => "Credenciales inválidas."));
    }
} else {
    // Si no se enviaron los datos necesarios, enviar mensaje de error
    http_response_code(400);
    echo json_encode(array("message" => "Faltan datos."));
}
}
?>
