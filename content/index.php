<?php
// Solar system directory
$system = dirname(dirname(__FILE__));

// set the include-path
set_include_path($system);

// load Solar
require_once "$system/source/solar/Solar.php";

// start Solar with system config file
$config = "$system/config/Solar.config.php";
Solar::start($config);

require_once "$system/source/foresmo/Foresmo.php";

// instantiate and run the front controller
$front = Solar::factory('Solar_Controller_Front');
$front->display();

// Done!
Solar::stop();
