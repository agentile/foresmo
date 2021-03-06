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
    'model_cache'   => array(
        'Solar_Cache',
        array(
            'adapter' => 'Solar_Cache_Adapter_File',
            'path' => "$system/tmp/cache",
            'hash' => false,
            'mode' => 0777,
        )
    ),
);

$config['Solar_Sql_Model'] = array(
    'cache' => 'model_cache',
    'auto_cache' => true,
    'prefix' => 'foresmo_'
);

$config['Solar_Sql_Model_Catalog']['classes'] = array('Foresmo_Model');

/**
 * sql connection
 */
$config['Solar_Sql']['adapter'] = 'Solar_Sql_Adapter_Mysql';

$config['Solar_Sql_Adapter_Mysql'] = array(
    'host' => 'localhost',
    'user' => 'taint',
    'pass' => 'taintpass',
    'name' => 'foresmo',
);

// Foresmo settings
$config['Foresmo']['installed'] = true;

// Foresmo Cache
$config['Foresmo']['cache'] = array(
    // which adapter class to use
    'adapter' => 'Solar_Cache_Adapter_File',
    // where the cache files will be stored
    'path' => '/tmp/Solar_Cache/',
    // the cache entry lifetime in seconds
    'life' => 1800,
);

// Authentication source
$config['Solar_Auth'] = array(
    'adapter' => 'Solar_Auth_Adapter_Sql',
);

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
