<?php

namespace App\Helpers; // Definimos el paquete donde está esta clase

// Importar la clase JWT para generar el token de usuario identificado y la clase DB para hacer consultas a la base de datos de Laravel
use Firebase\JWT\JWT;  // Importamos la clase JWT
use Illuminate\Support\Facades\DB; // Importamos la clase DB de Laravel para hacer consultas a la base de datos de Laravel
use App\Models\User; // Importamos el modelo User para hacer consultas a la tabla de usuarios de la base de datos de Laravel
use Illuminate\Support\Facades\Hash; // Importamos la clase Hash para cifrar contraseñas

class JwtAuth
{

    public $key; // Definimos una propiedad para la clave secreta

    public function __construct()
    { // Constructor de la clase
        $this->key = 'esto_es_una_clave_super_secreta-99887766'; // Definimos la clave secreta
    }
    public function singup($email, $password, $getToken = null)
    {
        // Buscar si existe el usuario con el correo electrónico proporcionado
        $user = User::where('email', $email)->first();
    
        // Comprobar si el usuario existe y si la contraseña es correcta
        if ($user && Hash::check($password, $user->password)) {
            // Generar el token de autenticación
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );
    
            // Codificar el token JWT
            $jwt = JWT::encode($token, $this->key, 'HS256');
    
            // Decodificar el token para obtener sus datos si es necesario
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
    
            // Devolver el token o los datos decodificados según corresponda
            if (is_null($getToken)) {
                return $jwt;
            } else {
                return $decoded;
            }
        } else {
            // Devolver un mensaje de error si las credenciales son incorrectas
            return ['status' => 'error', 'message' => 'Credenciales incorrectas'];
        }

    
    }
    public function checkToken($jwt, $getIdentity = false)
    {
        // Comprobar si el token es válido
        $auth = false;
        try {        
            $jwt = str_replace('"', '', $jwt); // Quitar las comillas dobles del token si las tiene para evitar errores al decodificarlo
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
    
        // Comprobar si el token decodificado contiene los datos del usuario
        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
    
        // Devolver los datos del usuario si es necesario
        if ($getIdentity) {
            return $decoded;
        }
    
        // Devolver el resultado de la comprobación del token
        return $auth;
    }

}    