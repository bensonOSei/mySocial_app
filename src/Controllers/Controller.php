<?php

namespace Benson\InforSharing\Controllers;

use Benson\InforSharing\Handlers\JsonHandler;
use Benson\InforSharing\Helpers\Request;
use Benson\InforSharing\Security\TokenGenerator;
use Benson\InforSharing\Helpers\Traits\CanHandleTokens;
use Benson\InforSharing\Helpers\Traits\CanHandleRequests;

class Controller
{
    use CanHandleTokens;

    protected Request $request;
    public function __construct()
    {

        $this->request = new Request();
        $this->request->__getAll();
        $this->tokenGenerator = new TokenGenerator();
    }

    public function auth()
    {
        $authUser = [];
        
        if($this->request->header_Authorization === null) {
            JsonHandler::respond([
                'message' => 'Unauthenticated'
            ], 401);
            exit();
        }
        $authenticated = $this->validateToken($this->request->header_Authorization);

        if ($authenticated === false) {
            JsonHandler::respond([
                'message' => 'Unauthorized'
            ], 401);
            exit();
        }

        // var_dump($authenticated);
        // exit;
        foreach ($authenticated as $key => $value) {
            $authUser[$key] = $value;
        }

        return (object) $authUser;
    }


}