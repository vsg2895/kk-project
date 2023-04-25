<?php namespace Jakten\DataObjects;

/**
 * Class Bounds
 * @package Jakten\DataObjects
 */
class Bounds {
    /**
     * @var $latitudeLow
     */
    public $latitudeLow;

    /**
     * @var $longitudeLow
     */
    public $longitudeLow;

    /**
     * @var $latitudeHigh
     */
    public $latitudeHigh;

    /**
     * @var $longitudeHigh
     */
    public $longitudeHigh;

    /**
     * Bounds constructor.
     *
     * @param $latitudeLow
     * @param $longitudeLow
     * @param $latitudeHigh
     * @param $longitudeHigh
     */
    public function __construct($latitudeLow, $longitudeLow, $latitudeHigh, $longitudeHigh)
    {
        $this->latitudeLow = $latitudeLow;
        $this->longitudeLow = $longitudeLow;
        $this->latitudeHigh = $latitudeHigh;
        $this->longitudeHigh = $longitudeHigh;
    }
}
