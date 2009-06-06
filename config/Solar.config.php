<?php
/**
 * all config values go in this array, which will be returned at the end of
 * this script
 */
$config = array();


/**
 * system and autoload-include directories
 */
$system = dirname(dirname(__FILE__));
$config['Solar']['system']  = $system;


/**
 * ini_set values
 */
$config['Solar']['ini_set'] = array(
    'error_reporting'   => (E_ALL | E_STRICT),
    'display_errors'    => true,
    'html_errors'       => true,
    'session.save_path' => "$system/tmp/session/",
    'date.timezone'     => 'UTC',
);


/**
 * auto-register some default objects for common use. note that these are
 * lazy-loaded and only get created when called for the first time.
 */
$config['Solar']['registry_set'] = array(
    'sql'           => 'Solar_Sql',
    'user'          => 'Solar_User',
    'model_catalog' => 'Solar_Sql_Model_Catalog',
);

$config['Solar_Sql_Model_Catalog']['classes'] = array('Foresmo_Model');

/**
 * sql connection
 */
$config['Solar_Sql']['adapter'] = 'Solar_Sql_Adapter_Mysql';

$config['Solar_Sql_Adapter_Mysql'] = array(
    'host' => 'localhost',
    'user' => 'user',
    'pass' => 'pass',
    'name' => 'foresmo',
    'prefix' => 'foresmo_'
);

// Foresmo settings
$config['Foresmo']['installed'] = true;

// Authentication source
$config['Solar_Auth'] = array(
    'adapter' => 'Solar_Auth_Adapter_Sql',
);

// Salt for password - change to something unique and strong.
$config['Solar_Auth_Adapter_Sql']['salt'] = 'g7qx01jusvg39c25s8';


/**
 * front controller
 */
$config['Solar_Controller_Front'] = array(
    'classes' => array('Foresmo_App'),
    'default' => 'index',
);

/**
 * done!
 */
return $config;
