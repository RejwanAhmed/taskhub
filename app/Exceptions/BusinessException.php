<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public function __construct($message = "Business rule violation", $code = 0)
    {
        parent::__construct($message, $code);
    }
}