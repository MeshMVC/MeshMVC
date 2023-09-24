<?php

	namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class View {
		private $from = ''; 	// from template
		private $filter = '';
		private $to = ''; 	// to selector -> default override all previous templates
		private $cache = false; // caching

		private $display_type = "html"; // html, text or json
		private $display_mode = "append"; // or prepend, append, replace, replace_inner

		private $use_models = true; // default: true (render models within brackets ex: "[user.email]") 
		private $doRenderOnDestruct = true;
		private static $counted = 0;

        // constructor requires filename of view
        public function __construct($from) {
            \MeshMVC\Cross::$currentView = $this;
			$this->from = $from;
			return $this;
        }

        // automatically render view on destruction
        public function __destruct() {
            if ($this->doRenderOnDestruct) $this->render();
            \MeshMVC\Cross::$currentView = null;
        }

        private function parseOutput($currentOutput) {
            \MeshMVC\Cross::$currentView = $this;
            //TODO: if cached with cache_key: output cache data
            return Queue::parse($this->from, $currentOutput, $this->filter, $this->to, $this->display_type, $this->display_mode, $this->use_models, 0);
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
			if (\MeshMVC\Environment::DEBUG) {
				self::$counted++;
				$myModel = new \MeshMVC\Model("template_".self::$counted, $from, "stats");
			}
			
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
