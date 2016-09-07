<?php

namespace SugarTranslations\Classes {
    
    class SugarTranslationsView {
        
        
        private $languages_catched = array();
        private $home_link_name = 'Grafinė vertimo aplinka';
        private $home_link = '<a href="/SugarTranslations.php">Grafinė vertimo aplinka</a>';
        private $home_link_separator = '&nbsp;&#9657;&nbsp;';
        
    
        public function __construct($languages_catched = array()) {
            
            $this->languages_catched = $languages_catched;
            
        }
        
        
        public function get($property_name) {
            if(property_exists($this, $property_name)) {
                return $this->$property_name;            
            } else {
                return null; // does not exist
            }        
        }
        
        
        private function getHTMLTemplateBegin($title = '', $subtitle = '') {
            
            $result = '';
            
            $result .= '<!DOCTYPE html>' . "\n";
            $result .= '<html>' . "\n";
            $result .=    '<head>' . "\n";
            $result .=        '<meta charset="utf-8">' . "\n";
            $result .=        '<title>' . $this->get('home_link_name') . $this->get('home_link_separator') . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>' . "\n";
            $result .=        '<style>' . "\n";
            $result .=         $this->getCSS() . "\n";
            $result .=        '</style>' . "\n";
            $result .=    '</head>' . "\n";
            $result .=    '<body>' . "\n";
            $result .=        '<h1>' . $this->get('home_link') . $this->get('home_link_separator') . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>' . "\n";
            
            if(isset($subtitle) && !empty($subtitle)) {
                $result .=    '<h2>' . htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') . '</h2>' . "\n";
            }
            
            return $result;
                        
        }
        
        
        private function getHTMLTemplateEnd($title = '') {
            
            $result = '';
            
            $result .=    '</body>' . "\n";
            $result .= '</html>' . "\n";
            
            return $result;
            
        }
        
        
        private function getCSS() {
            
            $return = <<<CSS
                * {
                    font-family: sans-serif;
                    font-size: 0.8 em;
                    margin: 0 auto;  /* align (center) */
                }
            
                table { 
                    /*border-collapse: collapse;*/
                    border-spacing: 0; /* cellspacing="0" */
                    padding-bottom: 150px;
                }  
                
                th, td { 
                    vertical-align: top; 
                    border: dashed 1px #ccc;
                    padding: 5px;
                }
            
                hr {
                    border: solid 1px #ccc;
                }
            
                h1 {
                    text-align: center;
                    margin:40px 0 20px 0;
                }
                    
                h1 a {
                    color: inherit;
                    text-decoration: none;
                }
                    
                h1 a:hover {
                    text-decoration: underline;
                }
                    
                h2 {
                    color: #C0C0C0;
                    text-align: center;
                    margin:5px 0 20px 0;
                }
                                            
                .c0 {
                    background-color: #009cc2;
                    color: #fff;
                }
            
                .c1 {
                    background-color: #F2F2F2;
                }
            
                .c2 {
                    background-color: #FFFFFF;
                }
            
                .column1 {
                    width: 15px;
                }
                
                .column2 {
                    width: 100px;
                }
            
                .column3, .column4, .column5 {
                    width: 250px;
                }
            
                .column6 {
                    width: 130px;
                }   
                    
                .language_file_list {
                    padding-bottom: 30px;
                }
                
                .language_file_list li {
                    padding:3px;
                }
                .button {
                    margin:0 0 20px 40px;
                }
CSS;
            
            return $return;
                        
        }
        
        
        public function displaySelectLanguageFile($data = array()) {
            
            $title = 'verčiamo failo pasirinkimas';
            
            echo $this->getHTMLTemplateBegin($title);
            
            echo $this->getFormUpgradeDictionaryDB();
            
            echo $this->getFormSelectLanguageFile($data);
            
            echo $this->getHTMLTemplateEnd($title);
            
        }
        
        
        private function getFormUpgradeDictionaryDB() {
            
            $result = ''
                   . '<form action="/SugarTranslations.php" method="get" target="_blank">'
                    . '<input class="button" type="submit" value="Užpildyti duomenimis automatinio vertimo duomenų bazę" />'
                    . '<input type="hidden" name="action" value="upgradeDictionaryDB" />'
                   . '</form>';
            
            //$result .= '';
            
            return $result;
            
        }
        
        
        private function getFormSelectLanguageFile($data) {
            
            $result = '';
            
            if(!isset($data) || !is_array($data) || empty($data)) {
                $result .= '<p>Sąrašas tuščias.</p>';
                return $result;
            }
                        
            $result .= '<ol class="language_file_list">' . "\n";
            foreach($data as $file) {
                $result .= '<li>' . "\n";
                
                $result .= '<a href="/SugarTranslations.php?action=translateFile&targetFileName=' . urlencode($file) . '">' . $file . '</a>' . (is_file($file) ? ('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . number_format((filesize($file) / 1024), 2, ',', ' ') . '&nbsp;KB') : '-') . "\n";
                
                $result .= '</li>' . "\n";
            }
            $result .= '</ol>' . "\n";
            
            return $result;
            
        }
        
        
        public function displayTranslationsTable($data = array(), $subtitle = '') {
            
            $title = 'verčiamas failas';
            
            echo $this->getHTMLTemplateBegin($title, $subtitle);
            
            echo $this->getFormTranslationsBody($data);
            
            echo $this->getHTMLTemplateEnd($title);
            
        }
        
        
        private function getFormTranslationsBody($data = array()) {
            
            $result = '';
            
            if(!isset($data) || !is_array($data) || empty($data)) {
                $data = array(); // let`s say that there is no data then...
            }
            
            $result .= '<table>';
            
            $result .= $this->getFormTranslationsBodyRowHeading();
            
            $css_class = 'c1';
            
            $counter = 0;
            foreach($data as $variable_name => $variable_info) {
                $counter++;
                
                $result .= '<tr class="' . $css_class . '">';
                $result .= $this->getFormTranslationsBodyRowContent($variable_info, $counter);
                $result .= '</tr>';
                
                $css_class = ($css_class == 'c1') ? 'c2' : 'c1';
                
            }
            
            $result .= '</table>';
            
            return $result;
            
        }
        
        private function getFormTranslationsBodyRowHeading() {
            
            $result = '';
            
            $languages = $this->get('languages_catched');
            
            $result .= '<th class="c0 column0">Eil. nr.</th>';
            $result .= '<th class="c0 column1">Rasta<br />viso</th>';
            $result .= '<th class="c0 column2">Vietos</th>';
            
            foreach($languages as $language) { // for each language detected..
                $result .= '<th class="c0 column3">Reikšmė <br />' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            
            $result .= '<th class="c0 column4">Automatinis vertimas</th>';
            $result .= '<th class="c0 column5">Galutinis vertimas</th>';
            $result .= '<th class="c0 column6"><!-- Kontrolė -->&nbsp;</th>';
            
            return $result;
            
        }
        
        
        private function getFormTranslationsBodyRowContent($variable_info = array(), $row_number = 0) {
            
            $result = '';
            
            if(!isset($data) || !is_array($data) || empty($data)) {
                $data = array(); // let`s say that there is no data then...
            }
            
            $result .= '<td>' . $row_number . '</td>';
            $result .= '<td>' . $this->getFormTranslationsBodyRowContentFilesCount($variable_info) . '</td>';
            $result .= '<td>' . $this->getFormTranslationsBodyRowContentFilesPathsAndNamesOfParentVariables($variable_info) . '</td>';
            
            $languages = $this->get('languages_catched');
            
            foreach($languages as $language) { // for each language detected..
                $result .= '<td>' . $this->getFormTranslationsBodyRowContentLanguageValue($language, $variable_info) . '</td>';
            }
            
            $result .= '<td>' . $this->getFormTranslationsBodyRowContentAutoTranslation($variable_info) . '</td>';
            
            $result .= '<td>' . $this->getFormTranslationsBodyRowContentFinalTranslation($variable_info) . '</td>';
            
            $result .= '<td>' . $this->getFormTranslationsBodyRowContentControlTranslation($variable_info) . '</td>';
            
            return $result;
            
        }
        
        
        private function getFormTranslationsBodyRowContentFilesCount($variable_info = array()) {
            
            $result = '';
            
            // Take care of input
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            // Action
            $counter = 0;
            foreach($variable_info as $language => $variable_info_inner) {
                if(!isset($variable_info_inner) || !is_array($variable_info_inner) || empty($variable_info_inner)) {
                    $counter_temp = 0;
                } else {
                    $counter_temp = count($variable_info_inner);
                }
                
                $counter += $counter_temp;
                
            }
            
            // Results
            $result .= (int)$counter;
            
            return $result;
            
        }
        
        
        private function getFormTranslationsBodyRowContentFilesPathsAndNamesOfParentVariables($variable_info = array()) {
            
            $result = '';
            
            // Take care of input
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            // Action
            $result = array();
            foreach($variable_info as $language => $variable_info_inner) {
                if(!isset($variable_info_inner) || !is_array($variable_info_inner) || empty($variable_info_inner)) {
                    // none
                } else {
                    foreach($variable_info_inner as $variable_file_name => $variable_info_inner_inner) {
                        if(!isset($variable_info_inner_inner) || !is_array($variable_info_inner_inner) || empty($variable_info_inner_inner)) {
                            // none
                        } else {
                            
                            foreach($variable_info_inner_inner as $VariableInfo) {
                                $result[] = $VariableInfo->variable_file_name . ' : ' . implode(':', $this->getKeysFromVariableInfoVariableDepth($VariableInfo)) . ' : ' . $VariableInfo->variable_name;
                            }
                            
                        }
                    }                    
                }                
            }
            //die(3);
            // Results
            //$result .= (int)$counter;
            
            return implode('<br /><hr />', $result);
            
        }
        
        
        private function getKeysFromVariableInfoVariableDepth($VariableInfo) {
            
            $result = array();
            
            if(!isset($VariableInfo->variable_depth) || !is_array($VariableInfo->variable_depth) || empty($VariableInfo->variable_depth)) {
                return (array)$result;
            }
            
            foreach($VariableInfo->variable_depth as $VariableKeyInfo) {
                if(isset($VariableKeyInfo->key)) {
                    $result[] = $VariableKeyInfo->key;
                }
            }
            
            return $result;
            
        }
        
        
        private function getFormTranslationsBodyRowContentLanguageValue($language_required = '', $variable_info = array()) { // e.g. $language = 'en_us'
            
            $result = '';
            
            // Take care of input
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            // Action
            $result = array();
            foreach($variable_info as $language => $variable_info_inner) {
                
                if($language != $language_required) {
                    continue; // get the next one
                }
                
                if(!isset($variable_info_inner) || !is_array($variable_info_inner) || empty($variable_info_inner)) {
                    // none
                } else {
                    foreach($variable_info_inner as $variable_file_name => $variable_info_inner_inner) {
                        if(!isset($variable_info_inner_inner) || !is_array($variable_info_inner_inner) || empty($variable_info_inner_inner)) {
                            // none
                        } else {
                            
                            foreach($variable_info_inner_inner as $VariableInfo) {
                                $result[$VariableInfo->variable_value] = htmlspecialchars($VariableInfo->variable_value, ENT_QUOTES, 'UTF-8');
                            }
                            
                        }
                    }                    
                }                
            }
            //var_dump($result); die();
            //die(3);
            // Results
            //$result .= (int)$counter;
            
            return implode('<br /><hr />', $result);
            
        }
        
        
        private function getFormTranslationsBodyRowContentAutoTranslation($variable_info = array()) {
            
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            $result = '';
            
            $result .= '<textarea>' . htmlspecialchars('Nesuprogramuota-4', ENT_QUOTES, 'UTF-8') . '</textarea>';
            
            return $result;
            
        }
        
        private function getFormTranslationsBodyRowContentFinalTranslation($variable_info = array()) {
            
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            $result = '';
            
            $result .= '<textarea>' . htmlspecialchars('Nesuprogramuota-5', ENT_QUOTES, 'UTF-8') . '</textarea>';
            
            return $result;
            
        }
        
        private function getFormTranslationsBodyRowContentControlTranslation($variable_info = array()) {
            
            if(!isset($variable_info) || !is_array($variable_info) || empty($variable_info)) {
                $variable_info = array(); // let`s say that there is no data then...
            }
            
            $result = array();
            
            $result[] = '<button onclick="alert(\'Nesuprogramuota\')">&#9660;</button><!--   Down -->';
            $result[] = '<button onclick="alert(\'Nesuprogramuota\')">&#9650;</button><!--   Up -->';
            $result[] = '<button onclick="alert(\'Nesuprogramuota\')">G</button><!--         Google Translate -->';
            
            return implode('&nbsp;&nbsp;', $result);
            
        }
        
        
    
        


    }
}