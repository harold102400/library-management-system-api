<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json'); 
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

require 'vendor/autoload.php';
require 'src/routes/routes.php';
