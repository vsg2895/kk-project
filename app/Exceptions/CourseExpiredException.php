<?php namespace Jakten\Exceptions;

/**
 * Class CourseFullException
 * @package Jakten\Exceptions
 */
class CourseExpiredException extends \Exception
{
    /**
     * CourseFullException constructor.
     * @param string $message
     * @param array $guards
     */
    public function __construct($message = 'Course expired', array $guards = [])
    {
        parent::__construct($message);
    }
}
