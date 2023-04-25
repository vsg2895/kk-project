<?php namespace Jakten\ModelMappers;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelMapper
 * @package Jakten\ModelMappers
 */
class ModelMapper
{
    /**
     * @param array $data
     * @param $modelClass
     *
     * @return Model
     */
    public function mapFromRequest(array $data, $modelClass)
    {
        $model = resolve($modelClass);

        return $this->map($data, with(new $model()));
    }

    /**
     * @param $data
     * @param Model $model
     *
     * @return Model
     */
    public function map($data, Model $model)
    {
        $fillable = array_flip($model->getFillable());
        $filteredData = array_intersect_key($data, $fillable);

        $model->fill($filteredData);

        return $model;
    }
}
