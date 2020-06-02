<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Genre;
use App\Model\Movie;

use App\Visitors\GenreVisitor;
use App\Visitors\MovieVisitor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class ApiMovieController extends Controller
{
    protected $perPage = 2;
    
    /**
     * Список фильмов
     * 
     * @param Request $request
     */
    public function list(Request $request)
    {
        $validator = $this->validateOrShow422($request->all(), [
            'page' => 'numeric|min:1',
            'sort' => Rule::in([
              'id', '-id',
              'name', '-name',
              'release_date', '-release_date',
            ]),
            'filter' => 'array|min:1',
            'filter.*' => 'exists:genres,id'
        ]);
        
        $query = Movie::resolveQuery(null); 
        
        if ($request->has('filter')) {
            $query = Movie::getGenre($query, $request->get('filter'));
        }
        
        if ($request->has('sort')) {
            $sort = $request->get('sort');
            
            /**
             * Минус в начале поля - сортируем в обратную сторону
             */
            $direction = (substr($sort, 0, 1)) == '-' ? 'desc' : 'asc';
            $sort = strtr($sort, ['-' => '']);
            
            $query = $query->orderBy($sort, $direction);
        }
        
        $count = $query->count();
        $page  = $request->has('page') ? (integer)$request->get('page') : 1;
        $max_page = (integer)ceil($count/$this->perPage);
        $max_page = ($max_page == 0) ? 1 : $max_page;
        
        $this->validateOrShow422($request->all(), ['page' => 'numeric|max:'.$max_page]);
        
        $query = Movie::getPage($query, $page, $this->perPage);
        $movies = $query->get();
        
        $movie_visitor = new MovieVisitor();
        $genre_visitor = new GenreVisitor();
        
        return response()->json([
            'data'   => $movie_visitor->visitCollection($movies),
            'genres' => $genre_visitor->visitCollection(Genre::all()),
            'page_details' => [
                'count'    => $count,
                'per_page' => $this->perPage,
                'page'     => $page,
                'max_page' => $max_page,
            ],
            'status_code' => 200,
        ]);
    }
    
    /**
     * Добавить фильм
     * 
     * @param Request $request
     */
    public function add(Request $request)
    {
        $validator = $this->validateOrShow422($request->all(), $this->makeValidatorRules());
        
        $movie = new Movie($request->all());
        if ($request->hasFile('image')) {
            $movie->image = $this->uploadImage($request->file('image'));
        }
        
        if ($movie->save()) {
            $movie->genres()->saveMany(Genre::find($request->get('genres')));
        }
        
        $visitor = new MovieVisitor();
        
        return response()->json([
          'data' => $visitor->visitElement($movie),
          'status_code' => 201,
        ], 201);
    }
    
    /**
     * Добавить фильм
     * 
     * @param integer $id ID из URL
     * @param Request $request
     */
    public function show($id, Request $request)
    {
        $movie = $this->findByIdOrShow404($id);
        
        $visitor = new MovieVisitor();
        
        return response()->json([
          'data' => $visitor->visitElement($movie),
          'status_code' => 200,
        ]);
    }
    
    /**
     * Обновить фильм
     * 
     * @param integer $id ID из URL
     * @param Request $request
     */
    public function update($id, Request $request)
    {
        $movie     = $this->findByIdOrShow404($id);
        $validator = $this->validateOrShow422($request->all(), $this->makeValidatorRules());
        
        $old_image = $movie->image;
        $movie->fill($request->all());
        
        if ($request->hasFile('image')) {
          $movie->image = $this->uploadImage($request->file('image'));
        } else {
          $movie->image = null;
        }
        
        if ($movie->save()) {
            $movie->genres = Genre::find($request->get('genres'));
            $movie->genres()->sync($request->get('genres'));
            
            if ($old_image) {
                $this->deleteImage($old_image);
            }
        }
        
        $visitor = new MovieVisitor();
        
        return response()->json([
          'data' => $visitor->visitElement($movie),
          'status_code' => 200,
        ]);
    }
    
    /**
     * Удалить фильм
     * 
     * @param integer $id ID из URL
     * @param Request $request
     */
    public function delete($id, Request $request)
    {
        $movie = $this->findByIdOrShow404($id);
        
        $movie->delete();
        
        $visitor = new MovieVisitor();
        
        return response()->json([
          'data' => $visitor->visitElement($movie),
          'status_code' => 202,
        ], 202);
    }
    
    /**
     * Ищем фильм или выбрасываем ошибку
     * 
     * @see \App\Exceptions\Handler
     * @param integer $id
     * @return Movie
     */
    protected function findByIdOrShow404($id)
    {
        $movie = Movie::find($id);
        
        if (!$movie) {
            abort(404, 'Movie not found');
        }
        
        return $movie;
    }
    
    /**
     * Правила для обработки формы, пока живут только в этом контроллере
     * 
     * @return array
     */
    protected function makeValidatorRules()
    {
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'image',
            'release_date' => 'required|date_format:d.m.Y',
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
        ];
        
        return $rules;
    }
    
    /**
     * Проверить данные или показать ошибки
     * 
     * @see \App\Exceptions\Handler
     * @param array $data
     * @param array $rules
     * @return type
     */
    protected function validateOrShow422($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        
        if (!$validator->passes()) {
            abort(422, implode(' ', $validator->messages()->all()));
        }
        
        return $validator;
    }
    
    /**
     * Загрузка файда из реквеста
     * 
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile $uploadedFile
     * @return string - абсолютный адрес
     */
    protected function uploadImage($uploadedFile)
    {
        $filename = md5(time()).'.'.$uploadedFile->getClientOriginalExtension();
        
        $disk = Storage::disk('movies');
        $disk->putFileAs('/', $uploadedFile, $filename);
        
        return $disk->url($filename);
    }
    
    /**
     * Удаление картинки, абсолютный url затирается
     * @param string $file
     */
    protected function deleteImage($file)
    {
        $disk = Storage::disk('movies');
        
        $file = strtr($file, [$disk->url('/') => '']);
        
        if ($disk->exists($file)) {
            $disk->delete($file);
        }
    }
}
