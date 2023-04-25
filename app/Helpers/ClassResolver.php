<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 2019-05-27
 * Time: 20:21
 */

namespace Jakten\Helpers;

/**
 * Trait ClassResolver
 * @package Jakten\Helpers
 */
trait ClassResolver
{

    /**
     * @param $variable
     * @param $class
     * @return mixed
     */
    public function checkIf($variable, $class) {
        return $variable instanceof $class ? $class : resolve($class);
    }
}
