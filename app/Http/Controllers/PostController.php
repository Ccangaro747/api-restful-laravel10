<?php

namespace App\Http\Controllers;

use App\Models\Post; //Añadimos el modelo Post para poder hacer uso de él en nuestro controlador de posts (PostController).
use Illuminate\Http\Response; //Añadimos la clase Response para poder devolver una respuesta HTTP personalizada en el método show de nuestro controlador de posts (PostController).
use App\Helpers\JwtAuth; //Añadimos el helper JwtAuth para poder hacer uso de él en nuestro controlador de posts (PostController).
use Illuminate\Http\Request; //Añadimos la clase Request para poder hacer uso de ella en nuestro controlador de posts (PostController).
use Illuminate\Support\Facades\Validator; //Añadimos la clase Validator para poder hacer uso de ella en nuestro controlador de posts (PostController).
use Illuminate\Support\Facades\Storage; // Asegúrate de importar la clase Storage
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage' , 'getPostsByCategory', 'getPostsByUser']]);
    }
    //Método que nos va a devolver todos los posts que tenemos en la base de datos.
    public function index() //Nos va a listar todos los posts que tenemos en la base de datos
    {
        $posts = Post::all()->load('category'); //Cargamos la relación con la categoría, para que nos devuelva también la categoría a la que pertenece cada post.

        return response()->json([  //Devolvemos una respuesta JSON con un array que contiene el código de la respuesta, el estado de la respuesta, y los posts que hemos obtenido de la base de datos.
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ]);
    }

    //Método que nos va a devolver un post en concreto.
    public function show($id)
    {
        $post = Post::find($id)->load('category'); //Cargamos la relación con la categoría, para que nos devuelva también la categoría a la que pertenece el post que hemos obtenido de la base de datos. Find nos va a devolver el post que tenga el id que le pasamos por parámetro.

        if (is_object($post)) { //Si el post que hemos obtenido de la base de datos es un objeto, devolvemos una respuesta JSON con un array que contiene el código de la respuesta, el estado de la respuesta, y el post que hemos obtenido de la base de datos.
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe'
            ];
        }

        return response()->json($data, $data['code']); //Devolvemos una respuesta JSON con el array que hemos creado anteriormente, y el código de la respuesta.
    }


    //Método que nos va a guardar un post en la base de datos.
    public function store(Request $request)
    {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json); //Objeto
        $params_array = json_decode($json, true); //Array


        //Si no está vacío el array de parámetros, validamos los datos. Si no, devolvemos un mensaje de error.
        if (!empty($params_array)) {

            //Conseguir el usuario identificado
            $user = $this->getIdentity($request);

            //Validar los datos
            $validate = Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el post, faltan datos'
                ];
            } else {
                //Guardar el post
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envía los datos correctamente'
            ];
        } {
            //Devolver una respuesta
            return response()->json($data, $data['code']);
        }
    }

    //Método que nos va a permitir actualizar un post en la base de datos.
    public function update($id, Request $request)
    {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true); //Array

        //Datos para devolver por defecto
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'No has enviado ningún post'
        ];

        if (!empty($params_array)) {
            //Validar los datos
            $validate = Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            //Quitar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            //Conseguir el usuario identificado
            $user = $this->getIdentity($request);


            //Buscar el registro a actualizar
            $post = Post::where('id', $id)
                ->where('user_id', $user->sub)
                ->first();

            if (!empty($post) && is_object($post)) {
                //Actualizar el registro en concreto
                $post->update($params_array);
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                ];
            }

            /*
            $where = [
                'id' => $id,
                'user_id' => $user->sub
            ];
            $post = Post::updateOrCreate($where, $params_array);
            */

            //Devolver algo
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post,
                'changes' => $params_array
            ];
        }

        return response()->json($data, $data['code']);
    }

    //Método que nos va a permitir borrar un post en la base de datos.
    public function destroy($id, Request $request)
    {
        //Conseguir el usuario identificado
        $user = $this->getIdentity($request);

        //Conseguir el registro
        $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

        if (!empty($post)) {
            //Borrarlo
            $post->delete();
            //Devolver algo
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function getIdentity($request)
    {
        //Conseguir el usuario identificado
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request)
    {
        //Recoger la imagen de la peticion
        $image = $request->file('file0');
        //Validar la imagen
        $validate = Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        //Guardar la imagen (storage disk - images)
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            Storage::disk('images')->put($image_name, File::get($image));
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        //Comprobar si existe el fichero
        $isset = Storage::disk('images')->exists($filename);
        if ($isset) {
            //Conseguir la imagen
            $file = Storage::disk('images')->get($filename);
            //Devolver la imagen
            return new Response($file, 200);
        } else {
            //Mostrar error
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
            return response()->json($data, $data['code']);
        }
    }

    public function getPostsByCategory($id)
    {
        $posts = Post::where('category_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function getPostsByUser($id)
    {
        $posts = Post::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
