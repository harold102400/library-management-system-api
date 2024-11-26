<?php

namespace Api\models;
use Api\database\DbConnection;
use \PDO;

class UserModel
{
    public $conn;

    public function __construct()
    {
        $instance = DbConnection::getInstance();
        $this->conn = $instance->getConnection();
    }

    public function userLogin(array $login_data)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return null; 
        }

        $stmt->execute([
            ":username" => $login_data["username"]
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($login_data["password"], $user["password"])) {
            return $user;
        }


        return null; 
    }

    public function getUser(int $id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->execute(["id" => $id]);
            $rows = $stmt->fetchColumn();
            return $rows;
        }
    }

    public function createUser(array $data_from_form)
    {
        $sql = "INSERT into users (username, email, password)VALUES (:username, :name, :password)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->execute([
                "username" => $data_from_form['username'],
                "email" => $data_from_form['email'],
                "passoword" => $data_from_form['password']
            ]);
            return $this->getUsernameFromDB($data_from_form['username']);
        }
    }

    public function getUsernameFromDB(string $username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":username" => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
