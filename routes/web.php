<?php

use Illuminate\Support\Facades\Route;
use App\Post;
use App\Category;

/*
RUTAS DE PRUEBA
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '.$nombre;
    return view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/animales', 'App\Http\Controllers\PruebasController@index');

Route::get('/test-orm', 'App\Http\Controllers\PruebasController@testOrm');


/*
RUTAS API
*/


// Rutas de prueba
Route::get('/user', 'App\Http\Controllers\UserController@pruebas');

Route::get('/posts', 'App\Http\Controllers\PostController@pruebas');

Route::get('/category', 'App\Http\Controllers\CategoryController@pruebas');

// Rutas del controlador de usuario

Route::post('/api/register', 'App\Http\Controllers\UserController@register');

Route::post('/api/login', 'App\Http\Controllers\UserController@login');

Route::post('/api/uptdate', 'App\Http\Controllers\UserController@uptdate');
