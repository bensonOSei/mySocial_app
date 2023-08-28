<?php

declare(strict_types=1);

namespace Benson\InforSharing\Controllers;

use Benson\InforSharing\Handlers\JsonHandler;
use Benson\InforSharing\Models\User;

class AuthController extends Controller
{
    private User $user;


    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }



    public function login()
    {
        $this->request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = $this->user->findByEmail($this->request->email, true);
        if (!$user) {
            return JsonHandler::respond([
                'message' => 'User not found'
            ], 404);
        }

        if (!password_verify($this->request->password, $user['password'])) {
            return JsonHandler::respond([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $this->createToken($user);

        return JsonHandler::respond([
            'token' => $token,
            'user' => $this->user->sendResponse($user)
        ]);
    }
}
