<?php

namespace SugarTranslations\Classes {

    class VariableKeyInfo {

        public $key = '';   // array/object key name, e.g. 'LBL_ID'
        public $type = '';  // e.g.: 'array', 'object'


        public function __construct($key, $type) {
            $this->key = $key;
            $this->type = $type;
        }

    }
    
}