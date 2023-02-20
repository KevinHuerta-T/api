<?php
include('vendor/autoload.php');
use \Firebase\JWT\JWT;

class User {
  private $conn;
  private $table_name = "users";

  public $id;
  public $first_name;
  public $last_name;
  public $email;
  public $password;
  public $role;
  public $jwt;

  public function __construct($db){
      $this->conn = $db;
  }

  function create(){
      $query = "INSERT INTO " . $this->table_name . "
                SET
                  first_name = :first_name,
                  last_name = :last_name,
                  email = :email,
                  password = :password,
                  role = :role";

      $stmt = $this->conn->prepare($query);

      $this->first_name=htmlspecialchars(strip_tags($this->first_name));
      $this->last_name=htmlspecialchars(strip_tags($this->last_name));
      $this->email=htmlspecialchars(strip_tags($this->email));
      $this->password=htmlspecialchars(strip_tags($this->password));
      $this->role=htmlspecialchars(strip_tags($this->role));

      $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

      $stmt->bindParam(":first_name", $this->first_name);
      $stmt->bindParam(":last_name", $this->last_name);
      $stmt->bindParam(":email", $this->email);
      $stmt->bindParam(":password", $password_hash);
      $stmt->bindParam(":role", $this->role);

      if($stmt->execute()){
          return true;
      }

      return false;
  }

 // Definir función para iniciar sesión
function login($email, $password, $db)
{
    // Consultar usuario por correo electrónico
    $query = "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró un usuario
    if ($user) {
        // Verificar si la contraseña es correcta
        if (password_verify($password, $user['password'])) {
            // Generar token JWT
            $jwt = $this->generateJWT($user);

            // Devolver el token
            return $jwt;
        } else {
            // Contraseña incorrecta
            return false;
        }
    } else {
        // Usuario no encontrado
        return false;
    }
}

// Definir función para generar token JWT
function generateJWT($user)
{
    // Definir datos a incluir en el token
    $token_data = array(
        "iss" => "http://example.com",
        "aud" => "http://example.com",
        "iat" => time(),
        "nbf" => time(),
        "exp" => time() + (60 * 60), // Expira en 1 hora
        "data" => array(
            "id" => $user['id'],
            "name" => $user['first_name'],
            "email" => $user['email'],
            "rol" => $user['role']
        )
    );

    // Codificar los datos en formato JWT
    $jwt = JWT::encode($token_data, "BwPw0&IMejBbRtPaU7#c86wF42370gDJDe", 'HS256');

    // Devolver el token
    return $jwt;
}

function logout(){
    // Actualizar la base de datos para eliminar el token JWT
    $query = "UPDATE " . $this->table_name . "
            SET
                jwt = NULL
            WHERE id = :id";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $this->id);
    $stmt->execute();
}

}
