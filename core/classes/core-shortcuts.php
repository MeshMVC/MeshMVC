<?php
	/* This file contains shortcuts to functions and classes within namespaces */

    function debug($obj) {
        if (\MeshMVC\Environment::DEBUG) {
            echo "<pre style='color: #333; font-family: source-code-pro, Menlo, Monaco, Consolas, 'Courier New', monospace; font-size:14px;'>";
            var_dump($obj);
            echo "</pre>";
        }
    }

	// query access
	function a($access_required) {
		return \MeshMVC\Tools::access($access_required);
	}
	function access($access_required) {
		return \MeshMVC\Tools::access($access_required);
	}
	
	
	// query url
	function q($argNumber_or_inPathString) {
		return \MeshMVC\Tools::queryURL($argNumber_or_inPathString);
	}
	function route($url) {
		return \MeshMVC\Tools::queryURL($url);
	}

	// query url
	function t($translate_string_id) {
		return \MeshMVC\Tools::translate($translate_string_id);
	}

	// View obj
	function view($filename) {
		\MeshMVC\Cross::$currentController->addView(new \MeshMVC\View($filename));
		return \MeshMVC\Cross::$currentView;
	}

	function needs($controllers) {
	    $current_this = \MeshMVC\Cross::$currentController;
		@$current_this->needs($controllers);
		return $current_this;
	}

	// Model obj
	function model($name, $instance = null) {
		// when no instance defined
		if ($instance == null) {
			// return model
			return \MeshMVC\Models::get($name);
		}

		// add new model by default
		@\MeshMVC\Cross::$models->add($name, $instance);
	}

	// Model Group obj
	function models() {
		return new \MeshMVC\Models();
	}
	function group() {
		return new \MeshMVC\Models();
	}
	function table() {
		return new \MeshMVC\Models();
	}
	function matrix() {
		return new \MeshMVC\Models();
	}

	// Database wrapper
	function db($query) {
		return new \MeshMVC\Database($query);
	}

	function posted($arg) {
		return \MeshMVC\Tools::Posted($arg);
	}
	function got($arg) {
		return \MeshMVC\Tools::Got($arg);
	}
	function redirect($url) {
		return \MeshMVC\Tools::redirect($url);
	}

?>
