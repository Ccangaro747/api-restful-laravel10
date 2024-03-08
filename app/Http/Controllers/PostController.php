<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function pruebas(request $request){
        return "Accion de pruebas de user POST";
    }
}
