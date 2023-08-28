<?php

namespace Benson\InforSharing\Handlers;

class ExceptionHandler extends \Exception
{
    // handle the exception
    public static function handle()
    {
        // Custom logic for handling exceptions
        // You can log the exception, display a friendly error page, etc.
        // Example: log the exception message
        error_log((new self)->getMessage());

        // Send an appropriate HTTP response
        http_response_code((new self)->getCode());
        echo JsonHandler::send([
            'error' => [
                'message' => (new self)->getMessage(),
                'file' => (new self)->getFile(),
                'line' => (new self)->getLine()
            ]
        ]);
        die();
    }
}