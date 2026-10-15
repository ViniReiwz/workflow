<?php

namespace Uspdev\Workflow\Exceptions;

use Exception;

class NoUserOnWorkflowObjectModelException extends Exception
{
    public function __construct(
        string $message = "Object related Model needs 'BelongsTo' relation with User.", 
        int $code = 404,
        Exception $previous = null
    ) 
    {
        parent::__construct($message, $code, $previous);
    }
}
