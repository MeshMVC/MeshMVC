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
	function View() {
		return new \MeshMVC\View();
	}

	// Model obj
	//_construct($var, $val, $id=null, $parent_id=null, $namespace="general")
	function Model($key="var", $val="", $space="general", $type="s") {
		return new \MeshMVC\Model($key, $val, $space, $type);
	}

	// Model Group obj
	function Models() {
		return new \MeshMVC\Models();
	}
	function Group() {
		return new \MeshMVC\Models();
	}
	function Table() {
		return new \MeshMVC\Models();
	}
	function Matrix() {
		return new \MeshMVC\Models();
	}

	// Database wrapper
	function DB() {
		return new MeshMVC\Database();
	}

	function Posted($arg) {
		return \MeshMVC\Tools::Posted($arg);
	}
	function Got($arg) {
		return \MeshMVC\Tools::Got($arg);
	}
	
?>
