<?php

namespace App\Visitors;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * Обойти коллекцию и вернуть урезанный массив
     * 
     * @param Collection $collection
     * @return array
     */
    public function visitCollection(Collection $collection)
    {
        $result = $collection->map(function ($item, $key) {
            return $this->visitElement($item);
        });
        
        return $result;
    }
    
    /**
     * Создаёт массив по указанному списку параметров из объекта модели
     * 
     * @param array $keys
     * @param Model $item
     * @return array
     */
    protected function copyByKey($keys, Model $item)
    {
        $new_item = [];
        
        foreach ($keys as $key) {
            $new_item[$key] = $item[$key];
        }
        
        return $new_item;
    }
}
