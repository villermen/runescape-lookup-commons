<?php

namespace Villermen\RuneScape\Exception;

class InvalidNameException extends RuneScapeException
{
    public function __construct(
        public readonly string $name,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
