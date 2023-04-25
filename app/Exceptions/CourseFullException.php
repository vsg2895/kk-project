<?php namespace Jakten\Exceptions;

/**
 * Class CourseFullException
 * @package Jakten\Exceptions
 */
class CourseFullException extends \Exception
{
    /**
     * CourseFullException constructor.
     * @param string $message
     * @param array $guards
     */
    public function __construct($message = 'Kursen du försöker boka är tyvärr fullbokad.', array $guards = [])
    {
        parent::__construct($message);
    }
}
