<?php

namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class View {
		public $from = ''; 	// from template
        public $filter = '';
        public $to = ''; 	// to selector -> default override all previous templates
        public $cache = false; // caching

        public $display_type = "html"; // html, text or json
        public $display_mode = "append"; // or prepend, append, replace, replace_inner

        public $use_models = true; // default: true (render models within brackets ex: "[user.email]")
        public $doRenderOnDestruct = true;
		private static $counted = 0;

        // constructor requires filename of view
        public function __construct($from) {
            \MeshMVC\Cross::$currentView = $this;
			$this->from = $from;
			return $this;
        }

        private function parseOutput($currentOutput) {
            \MeshMVC\Cross::$currentView = $this;
            //TODO: if cached with cache_key: output cache data
            return \MeshMVC\Queue::parse($this->from, $currentOutput, $this->filter, $this->to, $this->display_type, $this->display_mode, $this->use_models, 0);
        }

        //return as string
        public function toString() {
            \MeshMVC\Cross::$currentView = $this;
            $this->doRenderOnDestruct = false;
            return $this->parseOutput("");
        }

        // process render without output
		public function render() {
            \MeshMVC\Cross::$currentView = $this;
            \MeshMVC\Queue::$complete_output = $this->parseOutput(\MeshMVC\Queue::$complete_output);
			return true;
		}

		// set caching
		public function cache($setCaching) {
            \MeshMVC\Cross::$currentView = $this;
            $this->cache = $setCaching;
		}

		// send view as email
		public function email($filename) {
            \MeshMVC\Cross::$currentView = $this;
            // send email
		}

		// write to file
		public function export($filename) {
            \MeshMVC\Cross::$currentView = $this;
			//$this->render();
			//@file_put_contents($filename, $file_contents);
		}

		// from template filename
		public function from($from) {
            \MeshMVC\Cross::$currentView = $this;
            self::$counted++;
			$this->from = $from;
			return $this;
		}
		// filter applied on from
		public function filter($filter) {
            \MeshMVC\Cross::$currentView = $this;
			$this->filter = $filter;
			return $this;
		}
		// "to" selector
 		public function to($to) {
            \MeshMVC\Cross::$currentView = $this;
			$this->to = $to;
			return $this;
		}
		// html or text or json
		public function display_type($display_type) {
            \MeshMVC\Cross::$currentView = $this;
			$this->display_type = $display_type;
			return $this;
		}
		//  inner, prepend, append, replace
		public function by($display_mode) {
            \MeshMVC\Cross::$currentView = $this;
			$this->display_mode = $display_mode;
			return $this;
		}
		// use models in templates
		public function use_models($use_models) {
            \MeshMVC\Cross::$currentView = $this;
			$this->use_models = $use_models;
			return $this;
		}

	}
