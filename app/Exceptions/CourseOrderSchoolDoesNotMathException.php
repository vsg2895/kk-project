<?php namespace Jakten\Exceptions;

/**
 * Class CourseAndOrderSchoolDoesNotMathException
 * @package Jakten\Exceptions
 */
class CourseAndOrderSchoolDoesNotMathException extends \Exception
{
    /**
     * CourseAndOrderSchoolDoesNotMathException constructor.
     * @param string $message
     * @param array $guards
     */
    public function __construct($message = 'Kursen du försöker boka är tyvärr inte tillgänglig på den här skolan.', array $guards = [])
    {
        parent::__construct($message);
    }
}
