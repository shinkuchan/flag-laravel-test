<?php

namespace App\Visitors;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MovieVisitor extends AbstractVisitor
{
    protected $keys = ['id', 'name','description', 'image', 'release_date', 'genres'];
    
    public function visitElement(Model $item)
    {
        $genre_visitor = new GenreVisitor();
        
        $new_item = $this->copyByKey($this->keys, $item);
        $new_item['genres'] = $genre_visitor->visitCollection($item->genres);
        
        return $new_item;
    }
}
