<?php

namespace Api\helpers;

use Api\helpers\HttpResponses;

class Validations {
    public static function validate(array $data)
    {
        if(empty($data["title"]) || empty($data["author"]) || empty($data["year"]) ){
            echo json_encode(HttpResponses::notFound("These fields cannot be empty!"));
            return false;
        }

        if (is_null(json_decode($data["genre"], true)) && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(HttpResponses::notFound("This field must be a valid JSON!"));
            return false;
        }        
      
        if(!trim($data["title"]) || !trim($data["author"]) || !trim($data["year"])){
            
            echo json_encode(HttpResponses::notFound("These fields cannot have whitespaces!"));

            return false;
        }

        return true;  
    }
}