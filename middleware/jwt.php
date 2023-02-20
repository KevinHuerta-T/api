<?php

use \Firebase\JWT\JWT;

class JwtMiddleware {
    // Llave secreta para firmar los tokens
    private $secret_key = "BwPw0&IMejBbRtPaU7#c86wF42370gDJDe";
    private $token = null;

    public function validateToken() {
        if (!$this->token) {
            http_response_code(401);
            echo json_encode(array("message" => "Token no proporcionado"));
            exit;
        }

        try {
            // Decodificar el token y verificar si es válido
            $decoded = JWT::decode($this->token, $this->secret_key, array('HS256'));
            return $decoded;
        } catch (Exception $e) {
            // Si el token no es válido, enviar una respuesta de error
            http_response_code(401);
            echo json_encode(array("message" => "Token inválido"));
            exit;
        }
    }

    public function getTokenFromHeaders() {
        $headers = apache_request_headers();
    
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            $jwt = explode(" ", $auth_header)[1];
            $this->token = $jwt;
        }
    }

    public function decodeToken() {
        return JWT::decode($this->token, $this->secret_key, array('HS256'));
    }

    public function verifyToken() {
        $token = $this->token;
        $decoded = JWT::decode($token, $this->secret_key, array('HS256'));
        return $decoded;
    }
}
