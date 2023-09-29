<?php
// Load all config files in config folder
$configFiles = glob(PATH . "config/*.configs.php");
foreach ($configFiles as $configFile) {
    include $configFile;
}

// Load environment-specific config
$envConfigFile = PATH . "config/" . \MeshMVC\Config::ENV . ".env.php";
if (file_exists($envConfigFile)) {
    include $envConfigFile;
}

// Set error display settings based on debug mode
if (\MeshMVC\Environment::DEBUG) {
    // Debug mode: display errors
    ini_set("html_errors", "1");
    ini_set("error_prepend_string", "<pre style='color: #333; font-family: source-code-pro, Menlo, Monaco, Consolas, 'Courier New', monospace; font-size:14px;'>");
    ini_set("error_append_string", "</pre>");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Non-debug mode: suppress errors and warnings
    error_reporting(0);
    ini_set('display_errors', 0);
}