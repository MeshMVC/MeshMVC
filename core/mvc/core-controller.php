<?php

	namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class Controller {

		// unit tests results
		private $unit_tests = array();
		public $needed_controllers = array();

        public function __construct() {
            \MeshMVC\Cross:$currentController = $this;
        }

		// Unit Testing
		public function passed($log) {
			// log success test
			$this->note($log);
			$this->unit_tests[] = true;
		}
		public function failed($log) {
			// log failed test
			$this->note($log);
			$this->unit_tests[] = false;
		}

		public function unit_tests_passed() {
			if (count($this->unit_tests) > 0) {
				if (in_array(false, $this->unit_tests)) {
					return false;
				}
			}
			return true;
		}

		public function needs($controller_list) {
			if (is_array($controller_list)) {
				    // when argument is an array, add to controllers list
				foreach ($controller_list as $con) {
					$this->needed_controllers[] = trim($con);
				}
			} else {
				if (strpos($controller_list, ",") !== false) {
				    // when string is comma separated, treat it as an array
					$controllers_req = explode(",", $controller_list);
					foreach ($controllers_req as $con) {
						$this->needed_controllers[] = trim($con);
					}
				} else {
					$this->needed_controllers[] = trim($controller_list);
				}
			}
			return true;
		}

		// MVC shortcuts
		public function n($controller_list) {
			return $this->needs($controller_list);
		}
		public function r($controller_list) {
			return $this->needs($controller_list);
		}
		public function req($controller_list) {
			return $this->needs($controller_list);
		}
		public function requires($controller_list) {
			return $this->needs($controller_list);
		}
		public function View() {
			return new \MeshMVC\View();
		}
		public function Group() {
			return new \MeshMVC\Model();
		}
		public function Table() {
			return new \MeshMVC\Model();
		}
		public function Matrix() {
			return new \MeshMVC\Model();
		}

		// Misc Shortcuts
		public function note($log) {
			\MeshMVC\Tools::note($log);
		}
		public function q($arg) {
			\MeshMVC\Tools::queryURL($arg);
		}
		public function route($arg) {
			\MeshMVC\Tools::queryURL($arg);
		}
		public function a($arg) {
			\MeshMVC\Tools::access($arg);
		}
		public function access($arg) {
			\MeshMVC\Tools::access($arg);
		}
	}
