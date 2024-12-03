<?php

namespace Api\controllers;

use Api\helpers\ErrorLog;
use Api\helpers\HttpResponses;
use Api\models\UserModel;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

class UserController
{
    public function userLoginAuth($raw_data_from_endpoint)
    {
        try {   
            $login_data = [];
            if (isset($raw_data_from_endpoint['username'])) {
                $login_data["username"] = $raw_data_from_endpoint['username'];
            } else {
                $login_data["email"] = $raw_data_from_endpoint['email'];
            }
            
            $login_data["password"] = $raw_data_from_endpoint['password'];

            $userModel = new UserModel();
            $data_from_db = $userModel->userLogin($login_data);
            if ($data_from_db) {
                $now = time();
                $key = $_ENV['TOKEN_KEY'];
                $payload = [
                    'iss' => 'my-library-app',
                    'iat' => $now,
                    'exp' => $now + 86400,
                    'id' => (int)$data_from_db['user_id'],
                    'username' => $data_from_db['username'],
                ];
                $jwt = JWT::encode($payload, $key, 'HS256');
                $user_details = ["token" => $jwt, "user_id" => $data_from_db["user_id"], "display_name"=> $data_from_db['username'], ];
                echo json_encode($user_details);
            }
            else if (!$data_from_db) {
                echo json_encode(HttpResponses::notFound("Invalid username or password"));
            }
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    private function unauthorizedResponse($message = "Unauthorized")
    {
        echo json_encode(HttpResponses::unauthorizedUser($message));
        return;
    }

    public function getToken()
    {
        $headers = apache_request_headers();
        if (!isset($headers["Authorization"])) {
            return $this->unauthorizedResponse("Unauthenticated request");
        }
        $authorization = $headers["Authorization"];
        $authorization_array = explode(" ", $authorization);

        if (count($authorization_array) !== 2) {
            return $this->unauthorizedResponse("Token format is invalid");
        }
        $token = $authorization_array[1];
        try {
            $decoded_token = JWT::decode($token, new Key($_ENV['TOKEN_KEY'], 'HS256'));
            return $decoded_token;
        } catch (\Throwable $e) {
            return $this->unauthorizedResponse("Invalid token: " . $e->getMessage());
        }
    }

    public function validateToken()
    {
        $info_from_token_payload = $this->getToken();
        if (!$info_from_token_payload) {
           return;
        }
        $user = new UserModel();
        $jwt_user = $user->getUser($info_from_token_payload->id);
        if (!$jwt_user) {
            return $this->unauthorizedResponse("This ID doesn't exist!");
        }
        return $jwt_user;
    }

    public function createUser($data_from_form)
    {
        try {
            $hashed_password = password_hash($data_from_form['password'], PASSWORD_BCRYPT);
            $allData = [
                "username" => $data_from_form['username'],
                "email" => $data_from_form['email'],
                "password" => $hashed_password,
                "createdAt" => date('Y-m-d h:i:s')
            ];
            $user = new UserModel();
            $existing_username = $user->getUsernameFromDB($data_from_form["username"]);
            $existing_email = $user->getEmailFromDB($data_from_form['email']);
            if ($existing_username) {
                echo json_encode(HttpResponses::notFound("This username already exists!"));
                return;
            }
            if ($existing_email) {
                echo json_encode(HttpResponses::notFound("This email already exists!"));
                return;
            }
            $new_user = $user->createUser($allData);
            echo json_encode(HttpResponses::created($new_user));
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message n" . $error);
        }
    }

}
