<?php

namespace SugarTranslations\Classes {

    class VariableInfoVariableName {

        public $data_type = '';         // e.g. Possible value types: "boolean" "integer" "double" (for historical reasons "double" is returned in case of a float, and not simply "float") "string" "array" "object" "resource" "NULL" "unknown type"
        public $data_depth = array();   // Array keys represent depth (0 - root, last one - the deepest one). Array values represent data type and key/property of the data inside

    }
    
}