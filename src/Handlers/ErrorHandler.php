<?php

namespace Benson\InforSharing\Handlers;

use Dotenv\Dotenv;

class ErrorHandler extends \Exception
{
    private $debug;

    public function __construct()
    {
        $dotEnv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotEnv->load();
        $debug = $_ENV['APP_DEBUG'];
        $this->debug = $debug;
    }
    public static function handle(\Throwable $e, $code = 500): void
    {
        // Custom logic for handling errors
        // You can log the error, display a friendly error page, etc.
        // Example: log the error message
        error_log($e->getMessage());

        // Send an appropriate HTTP response
        http_response_code($code);

        if((new ErrorHandler())->debug == "true") {
            echo JsonHandler::send([
                'error' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                    
                ]
            ]);
        } else {
            echo JsonHandler::send([
                'error' => [
                    'message' => 'An error occurred'
                ]
            ]);
        }
        die();
    }

    public static function send($payload)
    {
        return JsonHandler::send([
            'error' => $payload
        ]);
    }

}
