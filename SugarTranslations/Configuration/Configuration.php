<?php


namespace SugarTranslations\Configuration {

    
    class Configuration {

        private $web_root_path = '';

        // Array with absolute paths (no slashes at the end!)
        //  inner subdirectories and files counts in
        private $sources_to_read_from = array();

        // Array with absolute paths (no slashes at the end!)
        //  inner subdirectories and files counts in
        // Bacause there are some very complex files in there...
        private $sources_to_avoid = array();

        // Array with extensions to look for, e.g. '.lang.php'.
        private $valid_extensions_of_sources_to_read_from = array();
        
        // PDO database handler (object)
        private $pdo_dbh = null; 
        
        public function __construct($web_root_path) {
            
            $this->set('web_root_path', $web_root_path);
            $this->set('sources_to_read_from');
            $this->set('sources_to_avoid');
            $this->set('valid_extensions_of_sources_to_read_from');
            $this->set('pdo_dbh');
            
        }
        
        
        private function set($property_name, $property_value = null) {
            if(!property_exists($this, $property_name)) {
                return false;
            }
                        
            switch($property_name) {
                case 'web_root_path':
                    $this->setWebRootPath($property_value);
                    return true;
                
                case 'sources_to_read_from':
                    $this->setSourcesToReadFrom();
                    return true;
                
                case 'sources_to_avoid':
                    $this->setSourcesToAvoid();                    
                    return true;
                    
                case 'valid_extensions_of_sources_to_read_from':
                    $this->setValidExtensionsOfSourcesToReadFrom();                    
                    return true;
               
                case 'pdo_dbh':
                    $this->setPdoDbh();
                    return true;
                
                default:                    
                    return false;
                    
            }
            
            return false;
            
        }
        
        
        private function setWebRootPath($property_value) {
            $this->web_root_path = $property_value;
        }
        
        
        private function setSourcesToReadFrom() {
            $this->sources_to_read_from  = array (
                $this->get('web_root_path') . DIRECTORY_SEPARATOR . 'modules'// . DIRECTORY_SEPARATOR . 'DocumentRevisions'
            );
        }
        
        
        private function setSourcesToAvoid() {
            $this->sources_to_avoid = array(
                $this->get('web_root_path') . '\include\SugarObjects\templates\company\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\SugarObjects\templates\file\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\SugarObjects\templates\file\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\SugarObjects\templates\file\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\SugarObjects\templates\issue\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\SugarObjects\templates\sale\language\application\en_us.lang.php',
                $this->get('web_root_path') . '\include\language\en_us.lang.php'
                //$this->get('web_root_path') . '\modules\Emails\language\en_us.lang.php',
                //$this->get('web_root_path') . DIRECTORY_SEPARATOR . 'include',
                //$this->get('web_root_path') . DIRECTORY_SEPARATOR . 'modules',
                //$this->get('web_root_path') . DIRECTORY_SEPARATOR . 'install/language\en_us.lang.php',
                //$this->get('web_root_path') 
            );
        }
        
        
        private function setValidExtensionsOfSourcesToReadFrom() {
            $this->valid_extensions_of_sources_to_read_from = array(
                '.lang.php'
            );
        }
        
        private function setPdoDbh() {
            try {
                $user = 'root';
                $pass = '';
                $host = 'localhost';
                
                $this->pdo_dbh = new \PDO('mysql:host=' . $host, $user, $pass);
                $this->createDBIfNotExist('vertimai123');
                
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "\n<br/>";
                die();
            }
        }
        
        
        private function createDBIfNotExist($dbname) {
            
            try {
            
                $dbname = "`" . str_replace("`", "``", $dbname) . "`";
                $this->pdo_dbh->query("CREATE DATABASE IF NOT EXISTS $dbname DEFAULT CHARACTER SET = 'utf8' DEFAULT COLLATE 'utf8_unicode_ci'");
                $this->pdo_dbh->query("USE $dbname");
            
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "\n<br/>";
                die();
            }
            
            
        }


        public function get($property_name) {
            if(property_exists($this, $property_name)) {
                return $this->$property_name;            
            } else {
                return null; // does not exist
            }        
        }

        
    }

    
}