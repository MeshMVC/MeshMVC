<?php

    // display core errors!
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	// init session
	session_start();

	// randomize seed
	srand();

	// set main path for files fetching
	define("PATH", $_SERVER["DOCUMENT_ROOT"]."/"); // Must include trailing slash

	// Includes
	require_once PATH."core/classes/core-logger.php";
	require_once PATH."core/classes/core-tools.php";
    require_once PATH."core/classes/core-config.php";

	// Configs
	require_once PATH."core/classes/core-load-configs.php";

	// routes (f=*, !q=*)
	require_once PATH."core/classes/core-route.php";
	
	// MVC
	require_once PATH."core/classes/core-db.php";
	require_once PATH."core/classes/core-model.php";
	require_once PATH."core/classes/core-models.php";
	require_once PATH."core/classes/core-cross.php"; @new \MeshMvc\Cross();

    // Storage
    require_once PATH."core/classes/core-storage.php";
    foreach (glob(PATH . "core/classes/core-storage-*.php") as $filename) {
        require_once $filename;
    }

    // Views
	require_once PATH."core/classes/core-view.php";
    foreach (glob(PATH . "core/classes/core-view-*.php") as $filename) {
        require_once $filename;
    }

    require_once PATH."core/classes/core-controller.php";
	require_once PATH."core/classes/core-shortcuts.php";

	// Execution queue start
	require_once PATH."core/classes/core-queue.php";
