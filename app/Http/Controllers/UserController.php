<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de UserController";
    }

    public function register(Request $request)
    {
        // Recoger los datos del usuario por POST
        $json = $request->input('json', null);
        $params = json_decode($json); // objeto
        $paramsArray = json_decode($json, true); // array

        if (!empty($params) && !empty($paramsArray)) {
            // Limpiar datos
            $paramsArray = array_map('trim', $paramsArray);

            // Validar los datos
            $validator = Validator::make($paramsArray, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', // Comprobar si el usuario existe (duplicado)
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                // La validación ha fallado
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors()
                ];
            } else {
                // Validación pasada correctamente

                // Cifrar la contraseña
                $pwd = Hash::make($params->password);

                // Crear el usuario
                $user = new User();
                $user->name = $paramsArray['name'];
                $user->surname = $paramsArray['surname'];
                $user->email = $paramsArray['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                // Guardar el usuario en la base de datos
                $user->save();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            ];
        }

        return response()->json($data, $data['code']);
    }

    // Método para loguear al usuario y conseguir el token de autenticación de usuario identificado por JWT (Json Web Token) 
    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth(); // Instanciar la clase JwtAuth para poder usar sus métodos y propiedades en este controlador UserController 


        // Recibir los datos por POST

        $json = $request->input('json', null);
        $params = json_decode($json); // objeto
        $paramsArray = json_decode($json, true); // array

        // Validar los datos

        $validator = Validator::make($paramsArray, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            // La validación ha fallado
            $singup = [
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no se ha podido logear',
                'errors' => $validator->errors()
            ];
        } else {
            // Validación pasada correctamente

            // Cifrar la contraseña

            $pwd = Hash::make($params->password);

            // Devolver el token o los datos decodificados según corresponda

            $singup = $jwtAuth->singup($params->email, $params->password);
            if (!empty($params->getToken)) {
                $singup = $jwtAuth->singup($params->email, $params->password, true);
            }
        }

        return response()->json($singup, 200); // Llamar al método singup de la clase JwtAuth para loguear al usuario y conseguir el token de autenticación de usuario identificado por JWT (Json Web Token) 
    }

    // Método para actualizar los datos del usuario identificado por JWT (Json Web Token)
    public function update(Request $request)
    {
        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth(); // Instanciar la clase JwtAuth para poder usar sus métodos y propiedades en este controlador UserController
        $checkToken = $jwtAuth->checkToken($token); // Obtener el usuario identificado

        // Verificar si el usuario está autenticado
        if ($checkToken) {
            //Recoger los datos por POST
            $json = $request->input('json', null);
            $params_array = json_decode($json, true);


            //Sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

        //Validar datos
        $validate = Validator::make($params_array, [
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'email' => 'required|email|unique:users,' . $user->sub
        ]);


        } else {
            //Si el token no es válido, se devuelve un mensaje de error
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no está identificado'
            ];
        }
        return response()->json($data, $data['code']); // Devolver el mensaje de error en formato json.
    }

    // Método para subir la imagen del usuario identificado por JWT (Json Web Token)
    public function upload(Request $request)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'El usuario no está identificado'
        ];
        return response()->json($data, $data['code'])->header('Content-Type', 'text/plain');
    }



}
