<?php

namespace Benson\InforSharing\Controllers;

use Benson\InforSharing\Handlers\JsonHandler;
use Benson\InforSharing\Models\User;

class UserController extends Controller
{

    private User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    /**
     * Create a user
     * 
     * @return
     */
    public function create()
    {

        $this->request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirm',
            'city' => 'required',
            'region' => 'required'
        ]);

        // hash the password
        $this->request->password = password_hash($this->request->password, PASSWORD_DEFAULT);

        // create the user
        if (!$this->user->create([
            'first_name' => $this->request->first_name,
            'last_name' => $this->request->last_name,
            'email' => $this->request->email,
            'password' => $this->request->password,
            'city' => $this->request->city,
            'region' => $this->request->region
        ]))
            return JsonHandler::respond([
                'message' => 'User not created'
            ], 500);



        // generate token
        $user = $this->user->findByEmail($this->request->email);
        $token = $this->createToken($user);


        return JsonHandler::respond([
            'token' => $token,
            'user' => $user
        ], 201);
    }



    /**
     * Get a user by id
     * 
     * @param int $id The id of the user
     * @return void Returns the user if found and null if not found
     */
    public function show(int $id)
    {
        return JsonHandler::respond($this->user->find($id));
    }

    /**
     * Update a user
     * 
     * @param int $id The id of the user to be updated
     * @return void Returns the user if found and null if not found
     */
    public function update(int $id)
    {
        $userId = $this->auth()->id;

        // Check if the user is authorized to perform this action
        // Only the user can update their own profile
        if ($userId !== $id) {
            return JsonHandler::respond([
                'message' => 'You are not authorized to perform this action'
            ], 401);
        }


        if ($this->user->find($id) === null) {
            return JsonHandler::respond([
                'message' => 'User not found'
            ], 404);
        }

        // Check request method
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $this->request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'city' => 'required',
                'region' => 'required'
            ]);

            if (!$this->user->edit([
                'first_name' => $this->request->first_name,
                'last_name' => $this->request->last_name,
                'email' => $this->request->email,
                'city' => $this->request->city,
                'region' => $this->request->region
            ], $id))
                return JsonHandler::respond([
                    'message' => 'User not updated'
                ], 500);
    
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

            $this->request->validateEither([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'city' => 'required',
                'region' => 'required'
            ]);

            if (!$this->user->edit($this->request->payload(), $id))
                return JsonHandler::respond([
                    'message' => 'User not updated'
                ], 500);
        }



        return JsonHandler::respond([
            'message' => 'User updated successfully'
        ], 201);
    }


    public function delete(int $id)
    {
        
    }


}
