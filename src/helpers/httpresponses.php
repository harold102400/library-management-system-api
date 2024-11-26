<?php

namespace Api\helpers;

class HttpResponses {
    public static $message = [
        'status' => '',
        'message' => ''
    ];

    public static function ok(string $res)
    {
        http_response_code(200);
        self::$message['status'] = 200;
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function created()
    {
        http_response_code(201);
        self::$message['status'] = 201;
        return self::$message['status'];
    }

    public static function noContent()
    {
        http_response_code(204);
        self::$message['status'] = 204;
        return self::$message;
    }

    public static function notFound(string $res = 'It seems that you are lost, please check the documentation')
    {
        http_response_code(404);
        self::$message['status'] = 404;
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function serverError(string $res = 'Internal Server Error')
    {
        http_response_code(500);
        self::$message['status'] = 500;
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function unauthorizedUser(string $res = "Unauthorized")
    {
        http_response_code(403);
        self::$message['status'] = 403;
        self::$message['message'] = $res;
        return self::$message;
    }
}