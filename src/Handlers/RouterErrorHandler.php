<?php

namespace Benson\InforSharing\Handlers;

use Exception;

class RouterErrorHandler extends ErrorHandler
{
    public static function handleRouteNotFound(): void
    {
        
        
        // Send an appropriate HTTP response
        http_response_code(404);
        echo JsonHandler::send([
            'error' => [
                'message' => "Route not found",
                'trace' => (new Exception)->getTrace()
            ]
        ]);

        die();
    }




}