<?php

namespace Api\models;

use Api\database\DbConnection;
use \PDO;

class ProductsModels {

    public $tableName = "books";
    public $conn;

    public function __construct()
    {
        $instance = DbConnection::getInstance();
        $this->conn = $instance->getConnection();
    }

    public function getAllBooks(int $page, int $limit, string $search = ''){
        $offset = ( $page - 1 ) * $limit;
        
        $sql = "SELECT * FROM $this->tableName";

        if (!empty($search)) {
            // $search = $this->conn->quote('%' . $search . '%');
            $sql.= " WHERE name LIKE '%'$search'%'";
        }
        $sql.= " LIMIT $limit OFFSET $offset;";
        
        $result = $this->conn->query($sql);

        if ($result) {
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            $countSql = "SELECT COUNT(*) as total FROM $this->tableName";

            if (!empty($search)) {
                $countSql .= " WHERE name LIKE $search";
            }


            $result = $this->conn->query($countSql);
            $totalCount = (int)$result->fetch(PDO::FETCH_ASSOC)['total'];
            return [
                'data' => $data,
                'totalCount' => $totalCount,
                'page' => $page,
                'limit' => $limit,
                'sql' => $sql
            ];
        }
    }

    public function create(array $new_book)
    {
        $sql = "INSERT INTO $this->tableName(title, author, year, genre, isFavorite, user_id, createdAt, updatedAt) VALUES (:title, :author, :year, :genre, :isFavorite, :user_id, :createdAt, :updatedAt)";
        $result = $this->conn->prepare($sql);
        if ($result) {
            $result->execute([
                ":title" => $new_book["title"],
                ":author" => $new_book["author"],
                ":year" => $new_book["year"],
                ":genre" => $new_book["genre"],
                ":isFavorite" => $new_book["isFavorite"],
                ":user_id" => $new_book["user_id"],
                ":createdAt" => $new_book["createdAt"],
                ":updatedAt" => null
            ]);
        }
    }

    public function getBook(int $id)
    {
        $sql = "SELECT * FROM $this->tableName WHERE id=:id";
        $result = $this->conn->prepare($sql);
        if ($result) {
            $result->execute([":id" => $id]);
            $book = $result->fetch(PDO::FETCH_ASSOC);
            return $book;
        }
    }

    public function update(array $data)
    {
        $sql = "UPDATE $this->tableName SET title=:title, author=:author, year=:year, genre=:genre, isFavorite=:isFavorite, user_id = :user_id, updatedAt = :updatedAt WHERE id=:id";
        $result = $this->conn->prepare($sql);
        if ($result) {
            $result->execute([
                "id"=> $data["id"],
                ":title" => $data["title"],
                ":author" => $data["author"],
                ":year" => $data["year"],
                ":genre" => $data["genre"],
                ":isFavorite" => $data["isFavorite"],
                ":user_id" => $data["user_id"],
                ":updatedAt" => $data["updatedAt"]
            ]);
        }
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM $this->tableName WHERE id=:id";
        $result = $this->conn->prepare($sql);
        if ($result) {
            $result->execute([
                "id" => $id
            ]);
        }
    }
}