<?php
	// load all config files in config folder
	$configs = \MeshMVC\Tools::search_files(PATH."config/*.configs.php");
	foreach ($configs as $config_file) {
		include $config_file;
	}

    // load environment specific config
    $env_config_file = PATH."config/".\MeshMVC\Config::ENV.".env.php";
    if (file_exists($env_config_file)) {
		include $env_config_file;
    }

    // only display error when in debug mode
	if (\MeshMVC\Config::DEBUG) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		ini_set('display_errors', 0);
	}

?>