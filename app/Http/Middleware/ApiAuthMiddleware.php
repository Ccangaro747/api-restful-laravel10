<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

                //Comprobar si el usuario esta identificado
                $token = $request->header('Authorization');
                $jwtAuth = new JwtAuth(); // Instanciar la clase JwtAuth para poder usar sus métodos y propiedades en este controlador UserController
                $checkToken = $jwtAuth->checkToken($token); // Obtener el usuario identificado

                // Verificar si el usuario está autenticado
        if ($checkToken ) {

            return $next($request);

    } else{
        // Devolver un error
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no está identificado'
        );
        return response()->json($data, $data['code']);
    }

}
}