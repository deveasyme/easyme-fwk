<?php

namespace Easyme\Util;

class Sanitizer {
    
    public function sanitize($value,$filter){
        switch($filter){
            
            case "boolean": return filter_var($value,FILTER_VALIDATE_BOOLEAN);
            case "email": return filter_var($value,FILTER_SANITIZE_EMAIL);
            case "encoded": return filter_var($value,FILTER_SANITIZE_ENCODED);
            case "magic_quotes": return filter_var($value,FILTER_SANITIZE_MAGIC_QUOTES);
            case "float": return filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            case "int": return filter_var($value,FILTER_SANITIZE_NUMBER_INT);
            case "special_chars": return filter_var($value,FILTER_SANITIZE_SPECIAL_CHARS);
            case "full_special_chars": return filter_var($value,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            case "string": return filter_var($value,FILTER_SANITIZE_STRING);
            case "stripped": return filter_var($value,FILTER_SANITIZE_STRIPPED);
            case "url": return filter_var($value,FILTER_SANITIZE_URL);
            case "unsafe_raw": return filter_var($value,FILTER_UNSAFE_RAW);
            
            case "trim": return trim($value);
            case "json": return $value ? json_decode($value,true) : $value;
                
            default : {
                if(is_callable($filter))
                    return $filter($value);
                else
                    throw new \Exception("Filtro de sanitização '$filter' não encontrado.");
            }
        }
        
        return $value;
    }
    
}

?>
