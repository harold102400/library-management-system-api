<?php

namespace Api\helpers;

use Api\helpers\HttpResponses;

class Validations {
    public static function validate(array $data)
    {
        if(empty($data["title"]) || empty($data["author"]) || empty($data["year"]) || empty($data["genre"])){
            echo json_encode(HttpResponses::notFound("These fields cannot be empty!"));
            return false;
        }

        if(!is_array($data["genre"])){
            echo json_encode(HttpResponses::notFound("This field has to be an array!"));
            return false;
        }
      
        if(!trim($data["title"]) || !trim($data["author"]) || !trim($data["year"])){
            
            echo json_encode(HttpResponses::notFound("These fields cannot have whitespaces!"));

            return false;
        }

        return true;  
    }
}