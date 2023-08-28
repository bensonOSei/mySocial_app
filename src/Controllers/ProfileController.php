<?php

namespace Benson\InforSharing\Controllers;

use Benson\InforSharing\Handlers\JsonHandler;
use Benson\InforSharing\Models\User;

class ProfileController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function test()
    {
        return JsonHandler::respond([
            'message' => 'Hello World'
        ]);
    }
}