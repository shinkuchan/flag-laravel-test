<?php

namespace App\Visitors;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GenreVisitor extends AbstractVisitor
{
    protected $keys = ['id', 'name'];
    
    /**
     * Конвертировать только один элемент
     * 
     * @param Model $item
     * @return array
     */
    public function visitElement(Model $item)
    {
        $new_item = $this->copyByKey($this->keys, $item);
        
        return $new_item;
    }

}
