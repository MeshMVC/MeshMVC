<?php

	namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class View {
		private $from = ''; 	// from template
		private $filter = '';
		private $to = ''; 	// to selector
		private $cache = false; // caching

		private $display_type = "html"; // html, text or json
		private $display_mode = "append"; // or prepend, append, replace, replace_inner

		private $use_models = true; // default: true (render models within brackets ex: "[user.email]") 
		private $rendered = false;
		private static $counted = 0;

        // constructor requires filename of view
        public function __construct($from) {
            global $current_this;
            $current_this = $this;
			$this->from = $from;
			return $this;
        }

        // automatically render view on destruction
        public function __toString() {
            $this->render($this->display_mode, null, $this->display_type);
            return true;
        }

        public function toString() {
			if (!$this->rendered) {
				//TODO: if cached with cache_key: output cache data
				return Queue::parse($this->from, 'render', $this->filter, $this->to, $this->display_type, $this->display_mode, $this->use_models, 0);
				$this->rendered = true;
 			}
        }

		// manual view render
		public function render($display_mode=null, $cache_key=null, $display_type=null) {
			if ($display_mode != null) $this->display_mode = $display_mode;
			if ($display_type != null) $this->display_type = $display_type;
			if (!$this->rendered) {
				//TODO: if cached with cache_key: output cache data
				@Queue::parse($this->from, 'render', $this->filter, $this->to, $this->display_type, $this->display_mode, $this->use_models, 0);
				$this->rendered = true;
 			}
			return true;
		}

		// set caching
		public function cache($setCaching) {
            $this->cache = $setCaching;
		}

		// send view as email
		public function email($filename) {
            // send email
		}

		// write to file
		public function export($filename) {
			//$this->render();
			//@file_put_contents($filename, $file_contents);
		}

		// from template filename
		public function from($from) {
			if (\MeshMVC\Config::DEBUG) {
				self::$counted++;
				$myModel = new \MeshMVC\Model("template_".self::$counted, $from, "stats");
			}
			
			$this->from = $from;
			return $this;
		}
		// filter applied on from
		public function filter($filter) {
			$this->filter = $filter;
			return $this;
		}
		// "to" selector
 		public function to($to) {
			$this->to = $to;
			return $this;
		}
		// html or text or json
		public function display_type($display_type) {
			$this->display_type = $display_type;
			return $this;
		}
		// replace_inner, prepend, append, replace
		public function display_mode($display_mode) {
			$this->display_mode = $display_mode;
			return $this;
		}
		// use models in templates
		public function use_models($use_models) {
			$this->use_models = $use_models;
			return $this;
		}


	}
