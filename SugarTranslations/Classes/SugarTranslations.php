<?php

namespace SugarTranslations\Classes {

    class SugarTranslations {

        private $sources_to_read_from = null;
        private $sources_to_avoid = null;
        private $valid_extensions_of_sources_to_read_from = null;
        
        protected $languages_catched = array(); // complete list of languages found inside

        //private $rearangeValues_depth = '';
        //private $rearangeValues_value = null;
        //private $rearangeValues_const = null;
        //private $rearangeValues_final = null;

        public  $error_message = null;

        
        // CONSTRUCTOR METHODS

        public function __construct($sources_to_read_from = array(), $sources_to_avoid = array(), $valid_extensions_of_sources_to_read_from = array()) {

            // $sources_to_read_from 
            $sources_to_read_from = (array)$sources_to_read_from; // if any type (string, integer, float, etc.) then array
            if(isset($sources_to_read_from) && is_array($sources_to_read_from) && !empty($sources_to_read_from)) {
                $sources_to_read_from = $this->fixSeparatorsOfSources($sources_to_read_from);
                $sources_to_read_from = $this->removeNotValidSources($sources_to_read_from);
                $this->set('sources_to_read_from', $sources_to_read_from);    
            } else {
                $this->set('error_message', 'Bad sources to read from (not set, not array or empty). Sources: ' . print_r($sources_to_read_from, true));
            }
            // END. $sources_to_read_from

            // $sources_to_avoid 
            $sources_to_avoid = (array)$sources_to_avoid; // if any type (string, integer, float, etc.) then array
            if(isset($sources_to_avoid) && is_array($sources_to_avoid) && !empty($sources_to_avoid)) {
                $sources_to_avoid = $this->fixSeparatorsOfSources($sources_to_avoid);
                $sources_to_avoid = $this->removeNotValidSources($sources_to_avoid);
                $this->set('sources_to_avoid', $sources_to_avoid);    
            } else {
                $this->set('error_message', 'Bad sources to read from (not set, not array or empty). Sources: ' . print_r($sources_to_read_from, true));
            }
            // END. $sources_to_read_from

            // $valid_extensions_of_sources_to_read_from 
            $valid_extensions_of_sources_to_read_from = (array)$valid_extensions_of_sources_to_read_from; // if any type (string, integer, float, etc.) then array
            if(isset($valid_extensions_of_sources_to_read_from) && is_array($valid_extensions_of_sources_to_read_from) && !empty($valid_extensions_of_sources_to_read_from)) {
                $this->set('valid_extensions_of_sources_to_read_from', $valid_extensions_of_sources_to_read_from);
            } else {
                $this->set('valid_extensions_of_sources_to_read_from', array());    
            }
            // END. $valid_extensions_of_sources_to_read_from

        }


        private function set($property_name, $property_value) {
            if(property_exists($this, $property_name)) {   

                switch($property_name) {
                    case'error_message':
                        if(!isset($this->$property_name)) {
                            $this->$property_name = '';
                        }

                        if(!empty($this->$property_name)) {
                            $this->$property_name .= "\n" . $property_value;
                        } else {
                            $this->$property_name = $property_value;
                        }
                        break;

                    default:
                        $this->$property_name = $property_value;
                        break;
                }

                return true;            

            }

            die('Property "' . $property_name . '" does not exist inside of "' . __CLASS__ . '" class. The value "' . (string)$property_value . '" cannot be set.'); // TODO: throw Exception

        }


        public function get($property_name) {
            if(property_exists($this, $property_name)) {
                return $this->$property_name;            
            } else {
                return null; // does not exist
            }        
        }


        private function fixSeparatorsOfSources($sources_to_read_from = array()) {
            if(isset($sources_to_read_from) && is_array($sources_to_read_from) && !empty($sources_to_read_from)) {
                foreach($sources_to_read_from as &$source_to_read_from) {
                    $source_to_read_from = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $source_to_read_from);
                }

                unset($source_to_read_from);
                reset($sources_to_read_from);

                return (array)$sources_to_read_from;

            }

            return null;

        }


        private function removeNotValidSources($sources = array()) {
            $result = array();

            if(isset($sources) && is_array($sources) && !empty($sources)) {
                foreach($sources as $source) {
                    if(is_dir($source)) {
                        // directory
                        $result[] = $source;
                    } elseif(is_file($source)) {
                        // file
                        $result[] = $source;
                    } else {
                        // not valid source
                        $this->set('error_message', 'Source "' . $source . '" is not valid. All sources must be in the form of absolute names. All sources must represent directory names or file names.');
                    }
                }
            }

            return (array)$result;

        }
        
        // END. CONSTRUCTOR METHODS
        
        
        /**
         *  Ši klasė kol kas parašyta taip, kad metodas 'scanFileToFindAllVariablesDefinedInside' įkrautų jam per argumentus paduodamus failus. Tokiu būdu žiūrima kokie nauji kintamieji atsirado...
         *  Kai kurie įkraunami failai savyje turi išplėstinės informacijos, su kuria reikia kažkaip susidoroti.
         *  Susidorojimo su išplėstine informacija būdai yra keli: 
         *  1) neįkelti failų turinio, kuriuose minima išplėstinė informacija (pvz. kitos klasės) -- tas turėtų būti apsprendžiama metodo 'scanFileToFindAllVariablesDefinedInside' viduje, prieš 'include'
         *  2) kelti failus, bet kartu įkelti ir reikiamus priedus (pvz. klasių deklaracijas ir pan.)
         * 
         *  Ši klasė įgyvendina 2 variantą iš dviejų paminėtų.
         * 
         */
        public function applyCompatibility() {
            // Beacause of: 'Not A Valid Entry Point';
            define('sugarEntry', TRUE); 

            // Because of: Fatal error: Class 'SugarThemeRegistry' not found in D:\www\p.sugarcrm\include\language\en_us.lang.php on line 1271
            //include_once __DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'SugarTheme' . DIRECTORY_SEPARATOR . 'SugarTheme.php';

        }


        public function scanAllSourcesToFindAllMatchingFiles() {

            $sources_to_read_from   = $this->get('sources_to_read_from');
            $sources_to_avoid       = $this->get('sources_to_avoid');
            $valid_extensions       = $this->get('valid_extensions_of_sources_to_read_from');

            $found_files = array();
            
            if(isset($sources_to_read_from) && is_array($sources_to_read_from) && !empty($sources_to_read_from)) {
                foreach($sources_to_read_from as $source_to_read_from) {
                    if(is_file($source_to_read_from) && $this->stringEndsWithAnyStringFromArray($source_to_read_from, $valid_extensions) && !$this->stringStartsWithAnyStringFromArray($source_to_read_from, $sources_to_avoid)) {
                        $found_files[] = $source_to_read_from;
                    } elseif(is_dir($source_to_read_from) && !$this->stringStartsWithAnyStringFromArray($source_to_read_from, $sources_to_avoid)) {

                        $new_files = $this->scanDirectoryRecursiveToFindAllMatchingFiles($source_to_read_from);
                        $found_files = array_merge($found_files, $new_files);

                    } else {
                        // $source_to_read_from is not a file, not a directory OR $source_to_read_from is directory, but has no files with valid extensions in it
                        // Do not need such
                    }
                }
            }

            return (array)$found_files;

        }


        private function scanDirectoryRecursiveToFindAllMatchingFiles($directory_absolute_path = '') {

            $result = array();   
            $sources_to_avoid = $this->get('sources_to_avoid');
            $valid_extensions = $this->get('valid_extensions_of_sources_to_read_from');


            if(is_dir($directory_absolute_path)) {
                $files_and_or_directories_inside = scandir($directory_absolute_path);
            } else {
                $files_and_or_directories_inside = array();
            }

            foreach($files_and_or_directories_inside as $file_or_directory) {
                if(($file_or_directory === '.') || ($file_or_directory === '..')) { // skip . and ..
                    continue;                                   // go to next
                }

                $file_or_directory_found = $directory_absolute_path . DIRECTORY_SEPARATOR . $file_or_directory;

                if(is_file($file_or_directory_found)) {
                    // this is a file with valid absolute path
                    $file_found = $file_or_directory_found;
                    if($this->stringEndsWithAnyStringFromArray($file_found, $valid_extensions) && !$this->stringStartsWithAnyStringFromArray($file_found, $sources_to_avoid)) {
                        $result[] = $file_found;    // count this in
                        continue;                                // go to next
                    }
                } elseif(is_dir($file_or_directory_found) && !$this->stringStartsWithAnyStringFromArray($file_or_directory_found, $sources_to_avoid)) {
                    // this is a directory with valid absolute path
                    $directory_found = $file_or_directory_found;
                    $files_found = $this->scanDirectoryRecursiveToFindAllMatchingFiles($directory_found);
                    $result = array_merge($result, $files_found);

                }
            }

            // unset all variables to be sure nothing is left
            if(isset($directory_absolute_path)) { unset($directory_absolute_path); }
            if(isset($valid_extensions)) { unset($valid_extensions); }
            if(isset($sources_to_avoid)) { unset($sources_to_avoid); }
            if(isset($files_and_or_directories_inside)) { unset($files_and_or_directories_inside); }
            if(isset($file_or_directory)) { unset($file_or_directory); }
            if(isset($file_or_directory_found)) { unset($file_or_directory_found); }
            if(isset($file_found)) { unset($file_found); }
            if(isset($directory_found)) { unset($directory_found); }
            if(isset($files_found)) { unset($files_found); }

            return (array)$result;

        }


        private function stringStartsWithAnyStringFromArray($string = '', $array_of_string = array()) {
            $string             = (string)$string;
            $array_of_string    = (array)$array_of_string;

            $result = false;

            if(isset($string) && is_string($string) && !empty($string)) {
                if(isset($array_of_string) && is_array($array_of_string) && !empty($array_of_string)) {

                    foreach($array_of_string as $string_from_array) {
                        if(strpos($string, $string_from_array) === 0) {
                            $result = true;

                            // unset all variables to be sure nothing is left
                            if(isset($string)) { unset($string); }
                            if(isset($array_of_string)) { unset($array_of_string); }
                            if(isset($string_from_array)) { unset($string_from_array); }

                            // return the result (true)
                            return (bool)$result;

                        } 
                    }
                }
            }

            // unset all variables to be sure nothing is left
            if(isset($string)) { unset($string); }
            if(isset($array_of_string)) { unset($array_of_string); }
            if(isset($string_from_array)) { unset($string_from_array); }

            // return the result (false)
            return (bool)$result;

        }




        private function stringEndsWithAnyStringFromArray($string = '', $array_of_string = array()) {
            $string             = (string)$string;
            $array_of_string    = (array)$array_of_string;

            $result = false;

            if(isset($string) && is_string($string) && !empty($string)) {
                if(isset($array_of_string) && is_array($array_of_string) && !empty($array_of_string)) {
                    foreach($array_of_string as $string_from_array) {
                        if(substr_compare($string, $string_from_array, -strlen($string_from_array), strlen($string_from_array)) === 0) {
                            $result = true;

                            // unset all variables to be sure nothing is left
                            if(isset($string)) { unset($string); }
                            if(isset($array_of_string)) { unset($array_of_string); }
                            if(isset($string_from_array)) { unset($string_from_array); }

                            // return the result (true)
                            return (bool)$result;

                        }
                    }
                }
            }

            // unset all variables to be sure nothing is left
            if(isset($string)) { unset($string); }
            if(isset($array_of_string)) { unset($array_of_string); }
            if(isset($string_from_array)) { unset($string_from_array); }

            // return the result (false)
            return (bool)$result;

        }


        public function scanEachCollectedFileToFindAllVariablesDefinedInside($collected_files_array = array()) {

            $defined_vars = array();

            if(isset($collected_files_array) && is_array($collected_files_array) && !empty($collected_files_array)) {

                foreach($collected_files_array as $file_with_absolute_path) {
                    $defined_vars[$file_with_absolute_path] = $this->scanFileToFindAllVariablesDefinedInside($file_with_absolute_path);
                }

            } else {
                // No values defined, because no files to search in
            }

            return (array)$defined_vars;

        }


        /*
         * Metodas, kuriam paduodam vieną argumentą - failą su absoliučiu keliu, pvz.: 'D:\www\p.sugarcrm\install\language\en_us.lang.php'
         * Paduodamo argumento pačiame metode nefiksuojame dėl šio metodo veikimo specifikos - t.y. šiame metode iki get_defined_vars() iškvietimo negalima kurti jokių metodo lygio kintamųjų
         * 
         */
        private function scanFileToFindAllVariablesDefinedInside() {
            if(is_file(func_get_arg(0))) {

                //$file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik__copy = $file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik;

                // There are two ways to check the file
                // 1) Parse it. Good - no impact to the system; Bad - never ending problems with detecting what is variable and what is not variable.
                // 2) Load it and check new variables defined. Bad - impact to the system; Good - almost perfect detection on what is variabe and what is not variable (however, problems can arise when detecting dynamic variable names).

                // The way #2 is chosen, because the purpose of this job/class is to look for languge files, which means:
                // Language files are loaded anyway (for example, loading system module will cause loading language file of that system module)
                // Language files contains mostly definitions, so no actions (e.g. CRUD) is performed inside of them
                // Language files are relatively safe to load, especially when overlooked before
                // TODO: make it safe as possible: parse file before including - check for sensitive keywords (e.g. for defined: classes, interfaces, language functions, global functions, namespaces, inner includes, inner requires, etc.)
                include func_get_arg(0);

                return get_defined_vars();

                /*
                if(
                        ($file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik__copy === $file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik)
                ) {
                    // Let`s say that two differet variables of this method did not change. 
                    // It means variables defined in this method was not overwritten by include. 
                    // It means we can drop our two private variables out.
                    $result = get_defined_vars();

                    if(isset($result['file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik'])) {
                        unset($result['file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik']);
                    }
                    if(isset($result['file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik__copy'])) {
                        unset($result['file_name_with_absolute_path__variable_name_should_be_unique_in_the_entire_system_0fgfkqppyl133xn9baik__copy']);
                    }

                    return $result;

                } else {
                    // Variables (or variable) defined in this method was overwritten by include. Leave them both as is.

                    return get_defined_vars();

                }*/
            }
        }


        public function rearangeValues($data_array = array()) {

            if(!isset($data_array) || !is_array($data_array) || empty($data_array)) {
                return $data_array; // nothing to do with such..
            }

            $result_array = array();

            //$security = 2;
            //$security_counter = 0;
            foreach($data_array as $file_path => $variable) {

                // SECURITY -TEMP
                //if($security_counter == $security) {
                    //return;
                //    print_r($result_array);
                    //die();
                //}
                //$security_counter++;
                //END. SECURITY -TEMP

                $variable_type = gettype($variable);
                switch($variable_type) {
                    case 'boolean':
                    case 'integer':
                    case 'double':
                    case 'string':

                        break;
                    case 'array':
                        $this->extractEachVariableOfArrayToResultArray($variable, $result_array, null, $file_path, $this->extractLanguageFromFilePath($file_path));
                        //var_dump($result_array); die();
                        break;
                    case 'object':
                        break;
                    case 'resource':
                        break;
                    case 'NULL':
                        break;
                    default:
                        break;

                }
            }

            return (array)$result_array;

        }

        private function extractLanguageFromFilePath($file_path = '') {

            $matches = null;
            $returnValue = preg_match('/.._..\\./', pathinfo($file_path, PATHINFO_BASENAME), $matches); // use '/.._..\\./', not '/^.._..\\./' because of modules\Calendar\Dashlets\CalendarDashlet\CalendarDashlet.en_us.lang.php ...
            if(isset($matches) && is_array($matches) && !empty($matches)) {
                $matches = substr(end($matches), 0, -1); // cut trailing dot
            }        
            
            return (string)$matches;

        }


        // $variable_key_to_save - null if first level
        private function extractEachVariableOfArrayToResultArray($variable, &$result_array, $variable_key_to_save = null, $file_path = '', $variable_language = '') {

            // Prepare $result_array if not prepared
            if(isset($result_array) && is_array($result_array)) {
                reset($result_array);
            } else {
                $result_array = array(); // Neturi reikėti priskirinėti masyvo...
                die('Nenumatyta klaida: tikimasi masyvo, o masyvo - nėra...');
            }

            $variable_type = gettype($variable);
            switch($variable_type) {
                case 'NULL':
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':      
                    $this->appendToLanguagesCatched($variable_language);
                    $value_catched = new \SugarTranslations\Classes\VariableInfo( (isset($variable_key_to_save->key) ? $variable_key_to_save->key : null), $variable_type, $variable, $file_path, $variable_language, (array)($this->key_block));
                    $result_array[] = $value_catched;
                    break;
                case 'array':
                    reset($variable); // reset to be sure

                    if(isset($variable_key_to_save)) { // can cause problems??..
                        $this->appendCurrentKeyToKeyBlock($variable_key_to_save);
                    }

                    // Go for deeper analysis..
                    $total = count($variable);
                    $counter = 0;
                    if(isset($variable) && is_array($variable) && !empty($variable)) {
                        foreach($variable as $_key => $_value) {
                            $counter++;
                            $this->extractEachVariableOfArrayToResultArray($_value, $result_array, $this->makeKeyObject($_key, gettype($_value)), $file_path, $variable_language);
                            $this->popCurrentKeyOfKeyBlockIfNeeded($counter, $total);
                        }                
                    } else {
                        $this->popCurrentKeyOfKeyBlockIfNeeded(0, 0);
                    }
                    break;

                case 'object':
                    // TODO: Probably have to look for the object case also...
                    break;
                case 'resource':
                    // TODO: Probably have to look for the resource case also...
                    break;
                //case 'NULL':
                //    break;
                default:
                    break;

            }

        }


        private function makeKeyObject($key, $type) {

            return (object)new \SugarTranslations\Classes\VariableKeyInfo($key, $type);        

        }

        private function appendCurrentKeyToKeyBlock($key = null) {
            if(!isset($this->key_block)) {
                $this->key_block = array();
            }

            reset($this->key_block);

            $this->key_block[] = $key;

            reset($this->key_block);

        }

        private function popCurrentKeyOfKeyBlockIfNeeded($current, $total_allowed) {
            if(!isset($this->key_block)) {
                $this->key_block = array();
            }

            reset($this->key_block);

            if($current == $total_allowed) { // if total
                array_pop($this->key_block);
                reset($this->key_block);
            }

        }
        
        
        protected function appendToLanguagesCatched($language) { // e.g. $language = 'en_us'
            
            // Take care on input
            /*
            if(!isset($this->languages_catched)) {
                $this->languages_catched = array();
            } elseif(!is_array($this->languages_catched)) {
                $this->languages_catched = (array)($this->languages_catched);
            }
            */
            
            // Action
            $this->languages_catched[$language] = $language; // unique array, unique values
                        
        }

        
        public function indexArrayByVariableNameAndLanguage($data = array()) {
            
            //$languages_catched = $this->get('languages_catched');
            
            $result = array();
            
            if(!isset($data) || !is_array($data) || empty($data)) {
                return $result;
            }
            
            foreach($data as $VariableInfo) { // $VariableInfo is object of class \SugarTranslations\Classes\VariableInfo
                if(!isset($VariableInfo->variable_name) || !isset($VariableInfo->variable_lang) || !isset($VariableInfo->variable_file_name)) {
                    // TODO: Throw exception
                    die('Problems with source "' . $VariableInfo->variable_file_name . '". No information on variable name (' . $VariableInfo->variable_name . ') or variable language (' . $VariableInfo->variable_lang . ') or file name of the source (' . $VariableInfo->variable_file_name . '). Check the source and try again after fixing the problem.');
                }
                
                // Take care of array structure..
                if(!isset($result[$VariableInfo->variable_name])) {
                    $result[$VariableInfo->variable_name] = array();
                }
                if(!isset($result[$VariableInfo->variable_name][$VariableInfo->variable_lang])) {
                    $result[$VariableInfo->variable_name][$VariableInfo->variable_lang] = array();
                }
                if(!isset($result[$VariableInfo->variable_name][$VariableInfo->variable_lang][$VariableInfo->variable_file_name])) {
                    $result[$VariableInfo->variable_name][$VariableInfo->variable_lang][$VariableInfo->variable_file_name] = array();
                }
                
                // Append data into the $result array
                $result[$VariableInfo->variable_name][$VariableInfo->variable_lang][$VariableInfo->variable_file_name][] = $VariableInfo;
                
            }
            
            if(isset($data)) { unset($data); }
            
            return (array)$result;
            
        }
        
        public function filterDataByFileName($data = array(), $file_name = '') {
            
            $result = array();
            
            if(!isset($data) || !is_array($data) || empty($data)) {
                return $result; // Empty result - empty data array
            }
            
            if(!isset($file_name) || empty($file_name) || !is_file($file_name)) {
                return $result; // Empty result - such file does not exist
            }
            
            reset($data);
            
            foreach($data as $var_name => $var_data) {
                
                if(isset($result[$var_name])) {
                    continue; // already inside, no need to include again
                }                
                
                $file_names_extracted = $this->filterDataByFileName_extractFileName($var_data);
                if(!isset($file_names_extracted) || empty($file_names_extracted) || !is_array($file_names_extracted)) {
                    continue;
                }
                
                if(in_array($file_name, $file_names_extracted)) {
                    $result[$var_name] = $var_data;
                }
                
            }
            
            if(isset($data)) { unset($data); }
            if(isset($var_name)) { unset($var_name); }
            if(isset($var_data)) { unset($var_data); }
            if(isset($file_names_extracted)) { unset($file_names_extracted); }
            if(isset($file_name)) { unset($file_name); }
            
            return $result;
            
        }
        
        private function filterDataByFileName_extractFileName($var_data = array()) {
            
            $result = array();
            
            if(!isset($var_data) || !is_array($var_data) || empty($var_data)) {
                return $result;
            }
            
            foreach($var_data as $lang => $vars_on_lang) {
                if(!isset($vars_on_lang) || !is_array($vars_on_lang) || empty($vars_on_lang)) {
                    continue;
                }
                
                foreach($vars_on_lang as $file_name => $vars_on_lang_file_name_data) {
                    $result[] = $file_name;
                }
            }
            
            return $result;
            
        }
        
        
        public function upgradeDictionaryDB($Configuration, $data = array(), $echo_queries = true) {
            
            $pdo_dbh = $Configuration->get('pdo_dbh');
            $result = true;
            
            $languages_catched_copy = $languages_catched = $this->get('languages_catched');
            
            if(!isset($languages_catched) || !is_array($languages_catched) || empty($languages_catched)) {
                return $result;
            }
            if(!isset($data) || !is_array($data) || empty($data)) {
                return $data;
            }
            
            
            
            //foreach($languages_catched as $language_from) {
            //    foreach($languages_catched_copy as $language_to) {
                    
                    $table_name = 'main_dictionary'; //$language_from . '_xhref_' . $language_to; // will be changed inside of $this->createTranslationTableIfDoesNotExist
                    $this->createTranslationTableIfDoesNotExist($this->filterDBTableName($table_name), $pdo_dbh, $echo_queries);
                    
                    $this->populateTranslationTableWithData($this->filterDBTableName($table_name), $data, $pdo_dbh);
                    
            //    }
            //}
            
            //var_dump($this->get('languages_catched')); die();
            
        }
        
        
        private function populateTranslationTableWithData($table_name, $data, $pdo_dbh) {
            
            try {
                
                $table_name = (string)$table_name;
                
                if(!isset($table_name) || empty($table_name)) {
                    return false;
                }
                
                if(!isset($data) || empty($data)) {
                    return true;
                } elseif(!is_array($data)) {
                    return false;
                }
                                
                $q = 'INSERT IGNORE INTO `' . $this->filterDBTableName($table_name) . '` '
                        . '(`variable_name`, `variable_type`, `variable_value`, `variable_file_name`, `variable_lang`, `variable_depth_serialized`) '
                        . 'VALUES (:variable_name, :variable_type, :variable_value, :variable_file_name, :variable_lang, :variable_depth_serialized)';
                $q = $pdo_dbh->prepare($q);
                
                //$q_data = array();
                //$pdo_dbh 
                
                foreach($data as $variable_name => $variables_info_based_on_languages) {
                    if(!isset($variables_info_based_on_languages) || empty($variables_info_based_on_languages) || !is_array($variables_info_based_on_languages)) {
                        continue;
                    }
                    
                    foreach($variables_info_based_on_languages as $language => $variables_info_based_on_files) {
                        
                        if(!isset($variables_info_based_on_files) || empty($variables_info_based_on_files) || !is_array($variables_info_based_on_files)) {
                            continue;
                        }
                        
                        foreach($variables_info_based_on_files as $array_of_VariableInfo) {
                            
                            if(!isset($array_of_VariableInfo) || empty($array_of_VariableInfo) || !is_array($array_of_VariableInfo)) {
                                continue;
                            }
                            
                            foreach($array_of_VariableInfo as $VariableInfo) {

                                $exists = $this->checkIfTranslationsTableHasSpecificDataRow($table_name, $data, $pdo_dbh, $VariableInfo);
                                
                                if(!isset($exists) || ($exists != true)) {
                                    $q->execute(array(
                                        ':variable_name'                => $VariableInfo->variable_name,
                                        ':variable_type'                => $VariableInfo->variable_type,
                                        ':variable_value'               => $VariableInfo->variable_value,
                                        ':variable_file_name'           => $VariableInfo->variable_file_name,
                                        ':variable_lang'                => $VariableInfo->variable_lang,
                                        ':variable_depth_serialized'=> serialize((array)($VariableInfo->variable_depth)),
                                    ));
                                    
                                }
                                
                                
                            }
                            
                        }
                        
                        //var_dump($variables_info_based_on_files); die();
                        
                    }
                    
                    
                }
                
                
                /*var_dump($data); die();
                
                
                
                
                $q = 
                   'CREATE TABLE IF NOT EXISTS `' . $this->filterDBTableName($table_name) . '` (
                            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `variable_name` TEXT NOT NULL               COMMENT :comment_variable_name                  COLLATE :encoding,
                            `variable_value` TEXT NULL                  COMMENT :comment_variable_value                 COLLATE :encoding,
                            `variable_language` TINYTEXT NOT NULL       COMMENT :comment_variable_language              COLLATE :encoding,
                            `variable_file` TEXT NOT NULL               COMMENT :comment_variable_file                  COLLATE :encoding,
                            `VariableInfoObject_serialized` TEXT NULL   COMMENT :comment_VariableInfoObject_serialized  COLLATE :encoding,
                            `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
                    )
                    COMMENT=:comment
                    COLLATE=:encoding
                    ENGINE=InnoDB;';
                
                $sth = $pdo_dbh->prepare($q, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
                $result = $sth->execute(array(
                     ':comment_id' => ''
                    ,':comment_variable_name' => 'Variable name, aka. variable index, aka. LBL name, e.g. LBL_DESCRIPTION'
                    ,':comment_variable_value' => 'Variable value, e.g. Label name'
                    ,':comment_variable_language' => 'Variable language, e.g. en-us'
                    ,':comment_variable_file' => 'Variable file name, relative to web root'
                    ,':comment_VariableInfoObject_serialized' => 'Serialized VariableInfo PHP object'
                    ,':comment' => 'Created by PHP script (' . __FILE__ . ', line:' . __LINE__ . ') at ' . date('Y-m-d H:i:s')
                    ,':encoding' => 'utf8_unicode_ci'
                ));
                */
                
                /*
                $pdo_dbh->query($pdo_dbh->quote($q));

                $pdo_dbh->quote()
                ' . ($table_name) . '*/
                // echo queries?
                //if(($echo_queries) && ($result)) {
                    //echo $pdo_dbh->quote($q) . "\n<br />";
                //}
                
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "\n<br/>";
                die();
            }            
            
            return true;
            
            
        }
        
        
        private function checkIfTranslationsTableHasSpecificDataRow($table_name, $data, $pdo_dbh, $VariableInfo) {
            // TODO: Get all at once into PHP cache/static and search in there. Line by line could be quite expensive...
            $q = 
               'SELECT 
                    COUNT(*)
                FROM 
                    `main_dictionary` 
                WHERE  
                    `variable_name` = :variable_name AND `variable_type` = :variable_type AND `variable_value` = :variable_value AND `variable_file_name` = :variable_file_name AND `variable_lang` = :variable_lang AND `variable_depth_serialized` = :variable_depth_serialized';
            
            
            $q = $pdo_dbh->prepare($q);
            $q->bindParam(':variable_name', $VariableInfo->variable_name);
            $q->bindParam(':variable_type', $VariableInfo->variable_type);
            $q->bindParam(':variable_value', $VariableInfo->variable_value);
            $q->bindParam(':variable_file_name', $VariableInfo->variable_file_name);
            $q->bindParam(':variable_lang', $VariableInfo->variable_lang);
            $q->bindParam(':variable_depth_serialized', $VariableInfo->variable_depth_serialized);
            
            $result = $q->execute();
            //var_dump($q->fetchColumn(0)); die();
            return (bool)($q->fetchColumn(0));
            
        }
                
        
        private function createTranslationTableIfDoesNotExist($table_name, $pdo_dbh, $echo_queries = true) {
            
            try {
                
                $q = 
                   'CREATE TABLE IF NOT EXISTS `' . $this->filterDBTableName($table_name) . '` (
                            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `variable_name` TEXT NOT NULL               COMMENT :comment_variable_name                  COLLATE :encoding,
                            `variable_type` TINYTEXT NOT NULL           COMMENT :comment_variable_type                  COLLATE :encoding,
                            `variable_value` TEXT NULL                  COMMENT :comment_variable_value                 COLLATE :encoding,
                            `variable_file_name` TEXT NULL              COMMENT :comment_variable_file_name             COLLATE :encoding,
                            `variable_lang` TINYTEXT NOT NULL           COMMENT :comment_variable_lang                  COLLATE :encoding,
                            `variable_depth_serialized` TEXT NULL       COMMENT :comment_variable_depth_serialized      COLLATE :encoding,
                            `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
                    )
                    COMMENT=:comment
                    COLLATE=:encoding
                    ENGINE=InnoDB;';
                
                $sth = $pdo_dbh->prepare($q, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
                $result = $sth->execute(array(
                     ':comment_id' => ''
                    ,':comment_variable_name' => 'Variable name, aka. variable index, aka. LBL name, e.g. LBL_DESCRIPTION'
                    ,':comment_variable_type' => 'Variable type defined by PHP gettype function'
                    ,':comment_variable_value' => 'Variable value, e.g. Label name'
                    ,':comment_variable_file_name' => 'Variable file name, relative to web root'
                    ,':comment_variable_lang' => 'Variable language, e.g. en-us'
                    ,':comment_variable_depth_serialized' => 'Serialized variable_depth array (one of properties owned by VariableInfo class)'
                    ,':comment' => 'Created by PHP script (' . __FILE__ . ', line:' . __LINE__ . ') at ' . date('Y-m-d H:i:s')
                    ,':encoding' => 'utf8_unicode_ci'
                ));
                
                
                /*
                $pdo_dbh->query($pdo_dbh->quote($q));

                $pdo_dbh->quote()
                ' . ($table_name) . '*/
                // echo queries?
                //if(($echo_queries) && ($result)) {
                //    echo $pdo_dbh->quote($q) . "\n<br />";
                //}
                
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "\n<br/>";
                die();
            }            
            
            return true;
            
        }
        
        
        private function filterDBTableName($table_name) {
            
            return preg_replace('/[^\da-z_]/i', '', $table_name);
            
        }


    }
}