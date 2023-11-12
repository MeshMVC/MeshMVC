<?php

    $_ENV["performance_start"] = microtime(true);

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

    // shortcuts
    require_once $_ENV["PATH"]."core/classes/core-shortcuts.php";

	// routes (f=*, !q=*)
	require_once $_ENV["PATH"]."core/classes/core-route.php";
	
	// Class containers
    require_once $_ENV["PATH"]."core/classes/core-models.php";
	require_once $_ENV["PATH"]."core/classes/core-cross.php"; @new \MeshMvc\Cross(); // TODO: move into queue

    // Abstracts
    foreach (glob($_ENV["PATH"] . "core/abstracts/*.php") as $filename) {
        require_once $filename;
    }

    // Abstract Extensions
    foreach (glob($_ENV["PATH"] . "core/seeded/*/*.php") as $filename) {
        require_once $filename;
    }

	// Execution queue start
	require_once $_ENV["PATH"]."core/classes/core-queue.php";
