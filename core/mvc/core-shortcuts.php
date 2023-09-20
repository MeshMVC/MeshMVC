<?php
	/* This file contains shortcuts to functions and classes within namespaces */

	// query access
	function a($access_required) {
		return MeshMVC\Tools::access($access_required);
	}
	function access($access_required) {
		return MeshMVC\Tools::access($access_required);
	}
	
	
	// query url
	function q($argNumber_or_inPathString) {
		return MeshMVC\Tools::queryURL($argNumber_or_inPathString);
	}
	function route($argNumber_or_inPathString) {
		return MeshMVC\Tools::queryURL($argNumber_or_inPathString);
	}

	// query url
	function t($translate_string_id) {
		return MeshMVC\Tools::translate($translate_string_id);
	}

	// View obj
	function view($filename) {
        global $current_this;
		@new \MeshMVC\View($filename).""; // trigger view __toString()
		return $current_this;
	}

	//
	function needs($controllers) {
        global $current_this;
		return $current_this->needs($controllers);
	}

	// Model obj
	function model($name, $value) {
		return new \MeshMVC\Models($key, $value);
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
		return new MeshMVC\Database($query);
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
