<?php namespace Jakten\Services\Schema;

/**
 * Interface SchemaInterface
 * @package Jakten\Services\Schema
 */
interface SchemaInterface
{
    /**
     * @param string $id
     * @return mixed
     */
    public function getLdJsonTag($id = "./");

    /**
     * @param bool $context
     * @param string $id
     * @return mixed
     */
    public function getLdJsonData($context = false, $id = "./");

    /**
     * @param $data
     * @return mixed
     */
    public function tryParse($data);

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return mixed
     */
    public function reset();

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);
}