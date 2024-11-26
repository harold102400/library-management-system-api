<?php

use Api\controllers\BookController;
use Api\helpers\HttpResponses;
use Api\controllers\UserLoginController;

$router = new \Bramus\Router\Router();

$router->get('/api', function(){
    echo "welcome";
});

$router->get('/api/books', function() {
    $books = new BookController();
    $books->getAllBooks();
});

$router->get('/api/books/{id}', function($id) {
    $book = new BookController();
    $book->getBook($id);
});

$router->post('/api/books', function() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $book = new BookController();
    $book->create($data);
});

$router->post('/api/register', function(){
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $new_user = new UserLoginController();
    $new_user->createUser($data);
});

$router->post('/api/auth', function(){
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $user = new UserLoginController();
    $user->userLoginAuth($data);
});

$router->put('/api/books/{id}', function($id) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $book = new BookController();
    $book->update($id, $data);
});

$router->delete('/api/books/{id}', function($id) {
    $book = new BookController();
    $book->delete($id);
});

$router->set404(function() {
    echo json_encode(HttpResponses::notFound("Sorry, but the page you are looking for does not exist!"));
});


$router->run();