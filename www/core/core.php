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

	// Configs
	require_once PATH."core/classes/core-load-configs.php";

	// routes (f=*, !q=*)
	require_once PATH."core/classes/core-route.php";
	
	// MVC
	require_once PATH."core/classes/core-db.php";
	require_once PATH."core/classes/core-model.php";
	require_once PATH."core/classes/core-models.php";
	require_once PATH."core/classes/core-cross.php"; @new \MeshMvc\Cross();

    // Views
	require_once PATH."core/classes/core-view.php";
    require_once PATH."core/classes/core-view-gql.php";
    require_once PATH."core/classes/core-view-html.php";
    require_once PATH."core/classes/core-view-json.php";
    require_once PATH."core/classes/core-view-text.php";
    require_once PATH."core/classes/core-view-openapi.php";

	require_once PATH."core/classes/core-controller.php";
	require_once PATH."core/classes/core-shortcuts.php";

	// Execution queue start
	require_once PATH."core/classes/core-queue.php";
