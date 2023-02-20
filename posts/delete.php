<?php
function delete(){
    // Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
require_once '/config/database.php';
require_once '/models/post.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare post object
$post = new Post($db);

// Get posted data and decode it
$data = json_decode(file_get_contents("php://input"));

// Check if the ID was provided
if (!empty($data->id)) {
    // Set post ID to be deleted
    $post->id = $data->id;

    // Delete the post
    if ($post->delete()) {
        // Set response code 200 OK and return success message
        http_response_code(200);
        echo json_encode(array("message" => "Post was deleted."));
    } else {
        // Set response code 503 Service Unavailable and return error message
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete post."));
    }
} else {
    // Set response code 400 Bad Request and return error message
    http_response_code(400);
    echo json_encode(array("message" => "Unable to delete post. No ID provided."));
}
}
?>