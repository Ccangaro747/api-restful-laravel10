<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = [
       'title', 'content', 'category_id', 'image' //Agregue image para que cuando edite un post se pueda actualizar la imagen
    ];

    // Relación uno a muchos inversa (muchos a uno) es decir, muchos posts pertenecen a un usuario
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    // Relación uno a muchos inversa (muchos a uno) es decir, muchos posts pertenecen a una categoría
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
