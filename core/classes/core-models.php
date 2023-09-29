<?php

namespace MeshMVC;

	// chainable class
	class Models {

        public static $models = Array();

		public function __construct($name, $value = null) {
		    if ($value == null) {
		        return self::$models[$name];
		    } else {
                self::$models[$name] = $value;
		    }
            return $this;
		}

        public static function add($name, $value) {
            self::$models[$name] = $value;
        }

        public function remove($name) {
            unset(self::$models[$name]);
			return $this;
        }

        public static function getAll() {
            return self::$models;
        }

        public static function get($name) {
            return self::$models[$name];
        }

	}
