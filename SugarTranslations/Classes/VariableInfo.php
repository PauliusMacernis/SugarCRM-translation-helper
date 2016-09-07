<?php

namespace SugarTranslations\Classes {

    class VariableInfo {

        public $variable_name = '';         // e.g. 'LBL_ID'
        public $variable_type = '';         // e.g. Possible value types: "boolean" "integer" "double" (for historical reasons "double" is returned in case of a float, and not simply "float") "string" "array" "object" "resource" "NULL" "unknown type"
        public $variable_value = null;      // depends on data type... Could be value of type: boolean, integer, float/double, string, NULL, (resource).
        public $variable_file_name = '';    // e.g. 'C:\dir\innerdir\innerinnerdir\file.ext'
        public $variable_lang = '';         // e.g. 'lt_lt'
        public $variable_depth = array();   // Array showing levels of the depth. It contains array of objects made of variableInfoVariableName class

        
        public function __construct($variable_name, $variable_type, $variable_value, $variable_file_name, $variable_lang, $variable_depth) {
            $this->variable_name        = $variable_name;
            $this->variable_type        = $variable_type;
            $this->variable_value       = $variable_value;
            $this->variable_file_name   = $variable_file_name;
            $this->variable_lang        = $variable_lang;
            $this->variable_depth       = $variable_depth;
        }

    }
    
}