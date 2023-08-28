<?php

namespace Benson\InforSharing\Middleware;

use Benson\InforSharing\Handlers\ErrorHandler;
use Benson\InforSharing\Handlers\JsonHandler;
use Benson\InforSharing\Helpers\Traits\CanHandleRequests;
use Benson\InforSharing\Helpers\Traits\CanHandleTokens;

class Authenticate
{
    use CanHandleTokens, CanHandleRequests;

    public function __construct()
    {
        $this->interceptAll();
    }
    public function handle()
    {

        if (!isset($this->header_Authorization)) {
            JsonHandler::respond([
                'message' => 'Unauthorized'
            ], 401);
            return false;
        }
    }
}
