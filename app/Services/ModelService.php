<?php namespace Jakten\Services;

use Illuminate\Database\Eloquent\Model;
use Jakten\ModelMappers\ModelMapper;

/**
 * Class ModelService
 * @package Jakten\Services
 */
class ModelService
{
    /**
     * @var ModelMapper
     */
    private $modelMapper;

    /**
     * ModelService constructor.
     *
     * @param ModelMapper $modelMapper
     */
    public function __construct(ModelMapper $modelMapper)
    {
        $this->modelMapper = $modelMapper;
    }

    /**
     * @param Model $model
     * @param array $data
     *
     * @return Model
     */
    public function updateModel(Model $model, array $data)
    {
        $model = $this->modelMapper->map($data, $model);

        return $model;
    }

    /**
     * @param $modelClass
     * @param array $data
     *
     * @return Model
     */
    public function createModel($modelClass, array $data)
    {
        $model = $this->modelMapper->mapFromRequest($data, $modelClass);

        return $model;
    }
}
