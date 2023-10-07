<?php

namespace MeshMVC;

	// chainable class
	class Model {

        private static $class_methods = [];

        public function __construct() {
            // Get \MeshMVC\Model class methods
            if (empty(self::$class_methods)) {
                $reflection = new \ReflectionClass($this);
                $parentClass = $reflection->getParentClass();
                if ($parentClass) {
                    self::$class_methods = array_map(
                        function ($method) {
                            return $method->getName();
                        },
                        $parentClass->getMethods(\ReflectionMethod::IS_PUBLIC)
                    );
                }
            }
            return $this;
        }

		public function save() {
			return $this;
		}

		public function load() {
			return $this;
		}

        public function json($withFunctions = true) {
            $reflection = new \ReflectionClass($this);
            $data = [];

            // Get class properties
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                if (strncmp($property, "__", 2) !== 0) {
                    // when property doesn't start with __, ge it
                    $propertyName = $property->getName();
                    $propertyValue = $property->getValue($this);
                    $data[$propertyName] = $propertyValue;
                }
            }

            if ($withFunctions) {
                // Get class methods
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    if (!in_array($methodName, self::$class_methods) && strncmp($methodName, "__", 2) !== 0) {
                        // when method isn't a \MeshMVC\Model methods and doesn't start with __, invoke it.
                        $methodOutput = $method->invoke($this);
                        $data[$methodName] = $methodOutput;
                    }
                }
            }

            // return JSON
            return json_encode($data);
        }
	}
