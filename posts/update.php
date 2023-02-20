<?php
function update(){
    // Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
require_once '../config/database.php';
require_once '../models/post.php';
require_once '../middleware/jwt.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare post object
$post = new Post($db);

// Instantiate JWT middleware
$jwt = new JwtMiddleware();

// Get posted data and decode it
$data = json_decode(file_get_contents("php://input"));

// Get token from headers
$jwt->getTokenFromHeaders();

// Validate token
if (!$jwt->validateToken()) {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
    exit;
}

// Get user ID and role from token
$decoded = $jwt->decodeToken();
$user_id = $decoded->data->id;
$rol = $decoded->data->rol;

// Check if user has permission to update post
if ($rol !== "rol_alto" && $rol !== "rol_alto_medio") {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied. User role does not have permission to update posts."));
    exit;
}

// Check if required data was provided
if (!empty($data->id) && !empty($data->title) && !empty($data->description)) {
    // Set post ID and other data to update
    $post->id = $data->id;
    $post->title = $data->title;
    $post->description = $data->description;

    // Update the post
    if ($post->update()) {
        // Set response code 200 OK and return success message
        http_response_code(200);
        echo json_encode(array("message" => "Post was updated."));
    } else {
        // Set response code 503 Service Unavailable and return error message
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update post."));
    }
} else {
    // Set response code 400 Bad Request and return error message
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update post. Required data is missing."));
}
}

?>