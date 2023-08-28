<?php

declare(strict_types=1);

namespace Benson\InforSharing\Database;

use Benson\InforSharing\Handlers\ErrorHandler;
use Dotenv\Dotenv;
use PDO;
use PDOException;

class Config
{
    private string $host;
    private string $port;
    private string $database;
    private string $username;
    private string $password;
    
    public function __construct()
    {
        // Load dotenv
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->host = $_ENV['DB_HOST'];
        $this->port = $_ENV['DB_PORT'];
        $this->database = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    public function connect()
    {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->database";
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            ErrorHandler::handle($e);
        } finally {
            $pdo = null;
        }
    }
}