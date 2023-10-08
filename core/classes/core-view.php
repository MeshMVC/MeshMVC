<?php

namespace MeshMVC;

	// Core Controller class for all controller objects to extend
	class View {
		public $from = ''; 	// from template
        public $filter = '';
        public $trim = '';
        public $to = ''; 	// to selector -> default override all previous templates
        public $cache = false; // caching

        public $display_type = "html"; // html, text or json
        public $display_mode = "append"; // or prepend, append, replace, replace_inner

        public $use_models = true; // default: true (render models within brackets ex: "[user.email]")
        public $doRenderOnDestruct = true;

        // constructor requires filename of view
        public function __construct($from) {
            \MeshMVC\Cross::$currentView = $this;

            if (is_object($from) && in_array('MeshMVC\Model', class_parents($from), true)) {
                $this->display_type = "json";
            }
			$this->from = $from;

			return $this;
        }

        public function parseOutput($currentOutput) {
            \MeshMVC\Cross::$currentView = $this;
            //TODO: if cached with cache_key: output cache data

            return \MeshMVC\Queue::parse($this->from, $currentOutput, $this->filter, $this->trim,  $this->to, $this->display_type, $this->display_mode, $this->use_models, 0);
        }

        //return as string
        public function toString() {
            \MeshMVC\Cross::$currentView = $this;
            $this->doRenderOnDestruct = false;
            return $this->parseOutput("");
        }

		// set caching
		public function cache($setCaching) {
            \MeshMVC\Cross::$currentView = $this;
            $this->cache = $setCaching;
            return $this;
		}

		// send view as email
        // TODO: send email
		public function email($subject, $email) {
            \MeshMVC\Cross::$currentView = $this;
            return $this;
		}

		// write to file
        // TODO: write with passed storage object instead, ex: SFTP, DB, S3, Session
		public function save($filename) {
            \MeshMVC\Cross::$currentView = $this;
			file_put_contents($filename, $this->parseOutput(""));
            return $this;
		}

		// from template filename
		public function from($from) {
            \MeshMVC\Cross::$currentView = $this;
			$this->from = $from;
			return $this;
		}

		// filter applied on from
		public function filter($filter) {
            \MeshMVC\Cross::$currentView = $this;
			$this->filter = $filter;
			return $this;
		}

        public function trim($filter) {
            \MeshMVC\Cross::$currentView = $this;
            $this->trim = $filter;
            return $this;
        }

		// "to" selector
 		public function to($to) {
            \MeshMVC\Cross::$currentView = $this;
			$this->to = $to;
			return $this;
		}
		// force display type (html or json) / this is usually done automatically by analysing the ->from property
		public function display_type($display_type) {
            \MeshMVC\Cross::$currentView = $this;
			$this->display_type = $display_type;
			return $this;
		}
		// HTML: inner, prepend, replace, append (default)
        // JSON: prepend, append, replace, merge (default)
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
