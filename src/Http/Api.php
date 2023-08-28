<?php


namespace Benson\InforSharing\Http;

use Benson\InforSharing\Handlers\JsonHandler;
use Dotenv\Dotenv;

class Api
{
    private string $key;
    private string $endpoint;

    

    public function __construct()
    {
        
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $this->key = $_ENV['API_KEY'];
        $this->endpoint = $_ENV['API_ENDPOINT'];
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    private function getHeaders()
    {
        return [
            'x-api-key: ' . $this->key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    public function payLoad(string $pdfID, array $message): string
    {
        return JsonHandler::send([
            'stream'=> true,
            'sourceId' => $pdfID,
            'messages' => $message
        ]);
    }

    private function curlOptions($payload): array
    {
        return [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $this->getHeaders(),
        ];
    }

    public function send(string $pdfID, $message)
    {
        $payload = $this->payLoad($pdfID, $message);
        $curl = curl_init();
        curl_setopt_array($curl, $this->curlOptions($payload));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){

            return JsonHandler::respond([
                'error'=> $err
            ]);
        }

        echo $response;

    }





}