<?php

namespace Villermen\RuneScape\Exception;

use Exception;
use Throwable;

class RuneScapeException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
