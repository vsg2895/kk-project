<?php namespace Jakten\Services\Schema;

/**
 * Abstract Class SchemaService
 * @package Jakten\Services\Schema
 */
abstract class SchemaService
{
    /**
     * @var array $properties
     */
    protected $properties = [];

    /**
     * Reset schema service.
     */
    public function reset()
    {
        if (array_key_exists("@type", $this->properties)) {

            $this->properties = [
                "@type" => $this->properties["@type"],
            ];
        }else{
            $this->properties = [];
        }
    }

    /**
     * Get Script tag whit Ld Json.
     *
     * @param string $id
     * @return string
     */
    public function getLdJsonTag($id = "./")
    {
        if (count($this->properties) > 1) {
            return '<script type="application/ld+json">' . $this->getLdJsonData(true, $id) . '</script>';
        } else {
            return '';
        }
    }

    /**
     * Get Ld Json data.
     *
     * @param bool $context
     * @param string $id
     * @return string
     */
    public function getLdJsonData($context = false, $id = "./")
    {
        $properties = $this->properties;
        if ($context) {
            $properties["@context"] = "http://schema.org";
        }

        if ($id != '') {
            $properties["@id"] = $id;
        }

        $returnString = '';
        ksort($properties);
        if (count($this->properties)) {
            $returnString = json_encode($properties, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        return $returnString;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->properties;
    }

    /**
     * Magic function, return value by property name.
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }
        return null;
    }

    /**
     * Magic function, set value by property name.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (is_array($value)) {
            if (count($value) >= 2) {
                $this->properties[$name] = $value;
            }
        } elseif (($value != "") && ($value != null)) {
            $this->properties[$name] = $value;
        }
    }
}