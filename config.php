<?php
/**
 * all config values go in this array, which will be returned at the end of
 * this script. vendor configs should modify this array directly.
 */
$config = array();

/**
 * system directory
 */
$system = dirname(__FILE__);
$config['Solar']['system'] = $system;

/**
 * default configs for each vendor
 */
include "$system/source/solar/config/default.php";
include "$system/source/foresmo/config/default.php";

/**
 * project overrides
 */

/**
 * done!
 */
return $config;
