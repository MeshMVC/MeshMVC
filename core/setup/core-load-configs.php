<?php

	// Configs
	// load all config files in config folder
	$configs = MeshMVC\Tools::search_files(PATH."config/*.configs.php");
	foreach ($configs as $config_file) {
		include $config_file;
	}

	if (MeshMVC\Config::DEBUG) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		ini_set('display_errors', 0);
	}

?>