<?php

namespace App\Visitors;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface VisitorInterface
{
    /**
     * Обойти коллекцию и вернуть упорядоченную информацию
     * 
     * @param Collection $collection
     */
    public function visitCollection(Collection $collection);
    
    /**
     * Конвертировать только один элемент
     * 
     * @param Model $item
     */
    public function visitElement(Model $item);
}
