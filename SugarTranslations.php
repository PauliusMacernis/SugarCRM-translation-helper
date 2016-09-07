<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Configuration'. DIRECTORY_SEPARATOR . 'Configuration.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Classes'      . DIRECTORY_SEPARATOR . 'SugarTranslations.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Classes'      . DIRECTORY_SEPARATOR . 'VariableKeyInfo.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Classes'      . DIRECTORY_SEPARATOR . 'VariableInfoVariableName.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Classes'      . DIRECTORY_SEPARATOR . 'VariableInfo.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SugarTranslations' . DIRECTORY_SEPARATOR . 'Classes'      . DIRECTORY_SEPARATOR . 'SugarTranslationsView.php';

use SugarTranslations\Configuration as STConfig;
use SugarTranslations\Classes as STClass;

$Configuration = new STConfig\Configuration(__DIR__); // __DIR__ must be web root path


// TODO: default internal charset, etc.


// Begin
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;


switch($action) {
    
    // show language files menu
    default:
    case 'selectLanguageFile':
        // Collect files
        $SugarTranslations = new STClass\SugarTranslations($Configuration->get('sources_to_read_from'), $Configuration->get('sources_to_avoid'), $Configuration->get('valid_extensions_of_sources_to_read_from'));
        //$SugarTranslations->applyCompatibility();
        $collected_files = array_unique($SugarTranslations->scanAllSourcesToFindAllMatchingFiles());
        
        // Soft data
        sort($collected_files);
        
        // Display data
        $view = new STClass\SugarTranslationsView(array());
        $view->displaySelectLanguageFile($collected_files);
        break;
    
    case 'translateFile':
        //die($_REQUEST['targetFileName']);
        // Collect files
        $SugarTranslations = new STClass\SugarTranslations($Configuration->get('sources_to_read_from'), $Configuration->get('sources_to_avoid'), $Configuration->get('valid_extensions_of_sources_to_read_from'));
        $SugarTranslations->applyCompatibility();
        $collected_files = array_unique($SugarTranslations->scanAllSourcesToFindAllMatchingFiles());

        // Collect data from files
        $collected_variables_and_values = $SugarTranslations->scanEachCollectedFileToFindAllVariablesDefinedInside($collected_files);

        // Rearange data found in files
        // - unindexed array of all variables found + attach more info on each variable
        $collected_variables_and_values_rearanged = $SugarTranslations->rearangeValues((array)$collected_variables_and_values);
        if(isset($collected_variables_and_values)) { unset($collected_variables_and_values); }

        // - index array by variable name, language
        $collected_variables_and_values_rearanged_by_var_and_lang = $SugarTranslations->indexArrayByVariableNameAndLanguage((array)$collected_variables_and_values_rearanged);
        if(isset($collected_variables_and_values_rearanged)) { unset($collected_variables_and_values_rearanged); }
        
        $collected_variables_and_values_rearanged_by_var_and_lang_file_only = array();
        if(isset($_REQUEST['targetFileName']) && !empty($_REQUEST['targetFileName']) && is_file(urldecode($_REQUEST['targetFileName']))) {
            $collected_variables_and_values_rearanged_by_var_and_lang_file_only = $SugarTranslations->filterDataByFileName((array)$collected_variables_and_values_rearanged_by_var_and_lang, urldecode($_REQUEST['targetFileName']));
            if(isset($collected_variables_and_values_rearanged_by_var_and_lang)) { unset($collected_variables_and_values_rearanged_by_var_and_lang); }
            
        }
        
        $view = new STClass\SugarTranslationsView($SugarTranslations->get('languages_catched'));
        $view->displayTranslationsTable($collected_variables_and_values_rearanged_by_var_and_lang_file_only, (isset($_REQUEST['targetFileName']) ? urldecode($_REQUEST['targetFileName']) : ''));
        break;
        
    case 'upgradeDictionaryDB':
        
        $started = microtime(true);
        
        $SugarTranslations = new STClass\SugarTranslations($Configuration->get('sources_to_read_from'), $Configuration->get('sources_to_avoid'), $Configuration->get('valid_extensions_of_sources_to_read_from'));
        $SugarTranslations->applyCompatibility();
        $collected_files = array_unique($SugarTranslations->scanAllSourcesToFindAllMatchingFiles());

        // Collect data from files
        $collected_variables_and_values = $SugarTranslations->scanEachCollectedFileToFindAllVariablesDefinedInside($collected_files);

        // Rearange data found in files
        // - unindexed array of all variables found + attach more info on each variable
        $collected_variables_and_values_rearanged = $SugarTranslations->rearangeValues((array)$collected_variables_and_values);
        if(isset($collected_variables_and_values)) { unset($collected_variables_and_values); }

        // - index array by variable name, language
        $collected_variables_and_values_rearanged_by_var_and_lang = $SugarTranslations->indexArrayByVariableNameAndLanguage((array)$collected_variables_and_values_rearanged);
        if(isset($collected_variables_and_values_rearanged)) { unset($collected_variables_and_values_rearanged); }
        
        $SugarTranslations->upgradeDictionaryDB($Configuration, $collected_variables_and_values_rearanged_by_var_and_lang);
        
        
        $finished = microtime(true);
        die('Atlikta per ' . number_format(($finished - $started), 2, ',', ' ') . ' s.');
        
        var_dump(reset($collected_variables_and_values_rearanged_by_var_and_lang)); die();
        break;
    
    
}

/*
die();

// Collect files
$SugarTranslations = new STClass\SugarTranslations($Configuration->get('sources_to_read_from'), $Configuration->get('sources_to_avoid'), $Configuration->get('valid_extensions_of_sources_to_read_from'));
$SugarTranslations->applyCompatibility();
$collected_files = array_unique($SugarTranslations->scanAllSourcesToFindAllMatchingFiles());

// Collect data from files
$collected_variables_and_values = $SugarTranslations->scanEachCollectedFileToFindAllVariablesDefinedInside($collected_files);

// Rearange data found in files
// - unindexed array of all variables found + attach more info on each variable
$collected_variables_and_values_rearanged = $SugarTranslations->rearangeValues((array)$collected_variables_and_values);
if(isset($collected_variables_and_values)) { unset($collected_variables_and_values); }

// - index array by variable name, language
$collected_variables_and_values_rearanged_by_var_and_lang = $SugarTranslations->indexArrayByVariableNameAndLanguage((array)$collected_variables_and_values_rearanged);
if(isset($collected_variables_and_values_rearanged)) { unset($collected_variables_and_values_rearanged); }

// Get languages involved
//$languages_catched = (array)($SugarTranslations->get('languages_catched'));

$view = new STClass\SugarTranslationsView($SugarTranslations->get('languages_catched'));
$view->displayTranslationsTable($collected_variables_and_values_rearanged_by_var_and_lang);
*/
//print_r($collected_variables_and_values_rearanged_by_var_and_lang);
//die();

