<?php
namespace Jakten\Services\Payment\Exceptions;

use Exception;

class StrategyNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Strategy not found');
    }
}
