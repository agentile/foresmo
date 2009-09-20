<?php
// Solar system directory
$system = dirname(dirname(__FILE__));

// set the include-path
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    set_include_path($system);
} else {
    set_include_path("$system/include");
}

// load Solar
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    require_once "$system/source/solar/Solar.php";
} else {
    require_once "Solar.php";
}

// start Solar with system config file
$config = "$system/config/Solar.config.php";
Solar::start($config);

require_once "$system/source/foresmo/Foresmo.php";

// instantiate and run the front controller
$front = Solar::factory('Solar_Controller_Front');
$front->display();

// Done!
Solar::stop();
