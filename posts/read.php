<?php
function read(){
    // Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
require_once '../config/database.php';
require_once '../models/post.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare post object
$post = new Post($db);

// Read posts
$stmt = $post->read();
$num = $stmt->rowCount();

// Check if more than 0 record found
if ($num > 0) {
    // Posts array
    $posts_arr = array();
    $posts_arr["data"] = array();

    // Retrieve table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $post_item = array(
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "created_at" => $created_at,
            "user" => array(
                "name" => $name,
                "role" => $role
            )
        );

        array_push($posts_arr["data"], $post_item);
    }

    // Set response code 200 OK and return posts data
    http_response_code(200);
    echo json_encode($posts_arr);
} else {
    // Set response code 404 Not found and return error message
    http_response_code(404);
    echo json_encode(array("message" => "No posts found."));
}
}
?>
