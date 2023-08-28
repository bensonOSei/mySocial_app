<?php

namespace Benson\InforSharing\Security;

use Dotenv\Dotenv;

class TokenGenerator
{
    private $secretKey;

    private $algo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $this->secretKey = $_ENV['APP_KEY'];
        $this->algo = $_ENV['APP_ENC_ALG'];
    }

    /**
     * Generate a token from the given array of data.
     *
     * @param array $data The data to be included in the token.
     * @return string The generated token.
     */
    public function generateToken(array $data): string
    {
        $payload = json_encode($data);
        $encodedPayload = base64_encode($payload);

        // Encrypt the payload with a secret key (optional but recommended for security)
        $encryptedPayload = openssl_encrypt(
            $encodedPayload,
            $this->algo,
            $this->secretKey,
            0,
            substr(md5($this->secretKey), 0, 16)
        );

        return $encryptedPayload;
    }

    /**
     * Retrieve the data from a given token.
     *
     * @param string $token The token to parse and retrieve data.
     * @return array|false The parsed data from the token, or false if the token is invalid.
     */
    public function parseToken(string $token)
    {
        // Decrypt the token with the secret key (optional but recommended for security)
        $decryptedPayload = openssl_decrypt(
            $token,
            $this->algo,
            $this->secretKey,
            0,
            substr(md5($this->secretKey), 0, 16)
        );

        if ($decryptedPayload === false) {
            return false;
        }

        $decodedPayload = base64_decode($decryptedPayload);

        if($decodedPayload === false)
            return false;

        // Parse the payload to retrieve the data
        $data = json_decode($decodedPayload, true);

        return $data;
    }
}
