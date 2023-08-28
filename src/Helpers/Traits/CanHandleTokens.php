<?php

namespace Benson\InforSharing\Helpers\Traits;

use Benson\InforSharing\Security\TokenGenerator;

trait CanHandleTokens
{
    use CanMemoize;
    private TokenGenerator $tokenGenerator;

    public function createToken($data)
    {
        return $this->tokenGenerator->generateToken($data);
    }

    public function validateToken($token): array | bool
    {
        // Get token from Bearer
        $token = explode(" ", $token)[1];

        $data = $this->memoize(
            'validateToken',
            function () use ($token) {

                return $this->tokenGenerator->parseToken($token);
            }
        );
        
        if ($data) {
            return $data;
        }
        return false;
    }
}
