<?php

	namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class Controller {

		// unit tests results
		private $unit_tests = [];

		public $loaded_views = [];

		public $needed_controllers = [];

        public function __construct() {
            \MeshMVC\Cross::$currentController = $this;
        }

        public function __destruct() {
        }

        public function addView($view) {
            \MeshMVC\Cross::$currentController = $this;
            $this->loaded_views[] = $view;
        }

		// Unit Testing
		public function passed($log) {
            \MeshMVC\Cross::$currentController = $this;
			// log success test
			$this->note($log);
			$this->unit_tests[] = true;
		}

		public function failed($log) {
            \MeshMVC\Cross::$currentController = $this;
			// log failed test
			$this->note($log);
			$this->unit_tests[] = false;
		}

		public function unit_tests_passed() {
            \MeshMVC\Cross::$currentController = $this;
			if (count($this->unit_tests) > 0) {
				if (in_array(false, $this->unit_tests)) {
					return false;
				}
			}
			return true;
		}

        public function needs($controller_list) {
            \MeshMVC\Cross::$currentController = $this;
            $controllers = is_array($controller_list) ? $controller_list : explode(",", $controller_list);
            $this->needed_controllers = array_merge($this->needed_controllers, array_map('trim', $controllers));
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
