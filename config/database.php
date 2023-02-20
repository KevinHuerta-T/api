<?php
// Archivo: config/database.php
// Descripción: configuración de la conexión a la base de datos

class Database {
    private $host = "localhost";
    private $db_name = "project";
    private $username = "root";
    private $password = "kevin123";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
