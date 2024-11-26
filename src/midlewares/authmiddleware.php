<?php

namespace Api\midlewares;

use Api\helpers\HttpResponses;
use Api\controllers\UserController;

class AuthMiddleware
{
    public static function handle()
    {
        $tokenController = new UserController();

        // Verificar si el token es válido
        if (!$tokenController->validateToken()) {
            exit; // Detenemos la ejecución de la solicitud
        }
    }
}
