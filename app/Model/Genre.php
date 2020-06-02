<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    /**
     * Название таблицы
     * 
     * @var string
     */
    protected $table = 'genres';
    
    /**
     * Нет временных меток
     * 
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Фильмы данного жанра
     */
    public function movies()
    {
        return $this->belongsToMany('App\Model\Movie', 'movies2genres', 'genre_id', 'movie_id');
    }
}
