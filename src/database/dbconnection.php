<?php

namespace Api\database;

use \PDO;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();


class DbConnection
{
    
    private static $instance = null;
    private $conn;

    private $host;
    private $user;
    private $pass;
    private $name;

    private function __construct()
    {
        $this->host = $_ENV['HOST'];
        $this->user = $_ENV['USER'];
        $this->pass = $_ENV['PASSWORD'];
        $this->name = $_ENV['DB'];
        $this->conn = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new DbConnection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
