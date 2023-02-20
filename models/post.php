<?php

class Post {
    private $conn;
    private $table_name = "posts";

    public $id;
    public $title;
    public $description;
    public $created_at;
    public $user_id;
    public $deleted_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT p.id, p.title, p.description, p.created_at, u.name, u.role
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.deleted_at IS NULL
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET title=:title, description=:description, created_at=:created_at, user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->created_at = date('Y-m-d H:i:s');
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":user_id", $this->user_id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET title=:title, description=:description
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function delete() {
        $query = "UPDATE " . $this->table_name . "
                  SET deleted_at=:deleted_at
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->deleted_at = date('Y-m-d H:i:s');
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":deleted_at", $this->deleted_at);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>
