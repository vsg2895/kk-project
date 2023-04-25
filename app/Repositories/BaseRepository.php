<?php namespace Jakten\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jakten\Repositories\Contracts\BaseRepositoryContract;

/**
 * Class BaseRepository
 * @package Jakten\Repositories
 */
abstract class BaseRepository implements BaseRepositoryContract
{
    /**
     * @var Builder
     */
    public $model;
    /**
     * @var App
     */
    private $app;

    /**
     * BaseRepository constructor.
     * @param App $app
     * @throws \Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->model;
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract protected function model();

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model->newQuery();
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function reset()
    {
        $this->makeModel();

        return $this;
    }

    /**
     * Handle cases where we want to chain methods but
     * need access to underlying query builder
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (count($arguments)) {
            return $this->query()->{$name}($arguments);
        }

        return $this->query()->{$name}();
    }
}
