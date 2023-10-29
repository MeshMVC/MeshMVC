<?php

    // display core errors!
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	// init session
	// session_start();

	// randomize seed
	srand();

	// set main path for files fetching
    $_ENV["PATH"] = realpath($_SERVER["DOCUMENT_ROOT"]."/../")."/";

	// Includes
	require_once $_ENV["PATH"]."core/classes/core-logger.php";
	require_once $_ENV["PATH"]."core/classes/core-tools.php";
    require_once $_ENV["PATH"]."core/classes/core-config.php";

	// Configs
	require_once $_ENV["PATH"]."core/classes/core-load-configs.php";

	// routes (f=*, !q=*)
	require_once $_ENV["PATH"]."core/classes/core-route.php";
	
	// MVC
	require_once $_ENV["PATH"]."core/classes/core-db.php";
	require_once $_ENV["PATH"]."core/classes/core-model.php";
	require_once $_ENV["PATH"]."core/classes/core-models.php";
	require_once $_ENV["PATH"]."core/classes/core-cross.php"; @new \MeshMvc\Cross();

    // Storage
    require_once $_ENV["PATH"]."core/classes/core-storage.php";
    foreach (glob($_ENV["PATH"] . "core/classes/core-storage-*.php") as $filename) {
        require_once $filename;
    }

    // Views
	require_once $_ENV["PATH"]."core/classes/core-view.php";
    foreach (glob($_ENV["PATH"] . "core/classes/core-view-*.php") as $filename) {
        require_once $filename;
    }

    require_once $_ENV["PATH"]."core/classes/core-controller.php";
	require_once $_ENV["PATH"]."core/classes/core-shortcuts.php";

	// Execution queue start
	require_once $_ENV["PATH"]."core/classes/core-queue.php";
