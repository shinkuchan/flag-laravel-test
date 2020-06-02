<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Movie extends Model
{
    /**
     * Название таблицы
     * 
     * @var string
     */
    protected $table = 'movies';
    
    /**
     * Массово заполняемые поля
     * 
     * @var array 
     */
    protected $fillable = ['name', 'description', 'release_date'];
    
    /**
     * Что считать датами
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'release_date'
    ];
    
    /**
     * Форматируем дату релиза в красивую
     * 
     * @param string $value
     * @return string
     */
    public function getReleaseDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d.m.Y');
    }
    
    /**
     * Жанры данного фильма
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres()
    {
        return $this->belongsToMany('App\Model\Genre', 'movies2genres', 'movie_id', 'genre_id');
    }
    
    /**
     * Проверка создана очередь или нет, если нет - создать
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function resolveQuery($query)
    {
        return (!$query) ? static::query() : $query;
    }

    /**
     * Взять список для нужной страницы
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query - очередь запроса
     * @param integer $page - номер страницы
     * @param integer $perPage - элементов на страницу
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getPage($query, $page, $perPage)
    {
        return static::resolveQuery($query)->forPage($page, $perPage);
    }
    
    /**
     * Отфильтровать фильмы, у которых жанры из списка
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query - очередь запроса
     * @param фккфн $genreIds - id жанров
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getGenre($query, $genreIds)
    {
        return static::resolveQuery($query)->whereHas('genres', function (Builder $query) use($genreIds) {
            $query->whereIn('id', $genreIds);
        });
    }
    
    /**
     * Извлекать все фильмы с жанрами
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(function (Builder $builder) {
            $builder->with('genres');
        });
    }
}
