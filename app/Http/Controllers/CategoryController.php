<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }
    public function show($id)
    {
        $category = Category::find($id);

        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoría no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }
    public function store(Request $request)
    {
        // Recoger los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json); // Objeto
        $params_array = json_decode($json, true); // Array

        if (!empty($params_array)) {
            // Validar los datos
            $validate = Validator::make($params_array, [
                'name' => 'required'
            ]);

            // Guardar la categoría
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoría'
                ];
            } else {
                $category = new Category();
                $category->name = $params->name;
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoría'
            ];
        }

        // Devolver el resultado
        return response()->json($data, $data['code']);
    }

    // Método para actualizar una categoría
    public function update($id, Request $request)
    {
        // Recoger los datos por post
        // Validar los datos
        // Quitar lo que no quiero actualizar
        // Actualizar el registro en concreto
        // Devolver algo

    }
}
