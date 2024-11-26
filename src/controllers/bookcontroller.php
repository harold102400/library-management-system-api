<?php


namespace Api\controllers;

use Api\helpers\ErrorLog;
use Api\helpers\HttpResponses;
use Api\models\BookModel;

class BookController {
    public $validate;
    public function __construct()
    {
        $token = new UserController();
        $this->validate = $token->validateToken();
    }
    public function getAllBooks()
    {
        $page = (int)@$_GET['page'] ?? 1;
        $limit = (int)@$_GET['limit'] ?? 10;
        $search = @$_GET['search'] ?? '';
        if ($page <= 0) {
            $page = 1;
        }
        if ($limit <= 0) {
           $limit = 10;
        }
        if ($limit > 100) {
            $limit = 100;
        }
        try {
            $book_instance = new BookModel();
            $all_books = $book_instance->getAllBooks($page, $limit, $search);
            echo json_encode($all_books);
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function create($new_book)
    {
        if (!$this->validate) {
            return;
        }
        try {
            $allData = [
                "title" => $new_book["title"],
                "author" => $new_book["author"],
                "year" => $new_book["year"],
                "genre" => json_encode($new_book["genre"]),
                "isFavorite" => $new_book["isFavorite"],
                "user_id" => $new_book["user_id"],
                "createdAt" => date('Y-m-d h:i:s'),
                "updatedAt" => null
            ];
            $book_instance = new BookModel();
            $book_instance->create($allData);
            echo json_encode(HttpResponses::created());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function getBook(int $id)
    {
        try {
            $book_instance = new BookModel();
            $book = $book_instance->getBook($id);
            if (!empty($book)) {
                echo json_encode($book);
                return;
            } else {
                echo json_encode(HttpResponses::notFound("The book with ID $id does not exist!"));
            }
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function update(int $id, array $data)
    {
        if (!$this->validate) {
            return;
        }
        try {
            $allData = [
                "id" => $id,
                "title" => $data["title"],
                "author" => $data["author"],
                "year" => $data["year"],
                "genre" => json_encode($data["genre"]),
                "isFavorite" => $data["isFavorite"],
                "user_id" => $data["user_id"],
                "updatedAt" => $data["updatedAt"]
            ];

            $book_instance = new BookModel();
            $book_instance->update($allData);
            echo json_encode(HttpResponses::ok("Book with id " . $id . " has been updated."));
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function delete(int $id)
    {
        if (!$this->validate) {
            return;
        }
        try {
            $book_instance = new BookModel();
            $book_instance->delete($id);
            echo json_encode(HttpResponses::noContent());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

}