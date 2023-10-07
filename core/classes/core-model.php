<?php

namespace MeshMVC;

	// chainable class
	class Model {
		public function __construct () {
			return $this;
		}

		public function save() {
			return $this;
		}

		public function load() {
			return $this;
		}

        public function json($withFunctions = true)
        {
            $reflection = new \ReflectionClass($this);
            $data = [];

            // Get class properties
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $propertyValue = $property->getValue($this);
                $data[$propertyName] = $propertyValue;
            }

            if ($withFunctions) {
                // Get class methods
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    if (!in_array($methodName, ["save", "load", "json", "__construct"])) {
                        $methodOutput = $method->invoke($this);
                        $data[$methodName] = $methodOutput;
                    }
                }
            }

            // Convert data array to JSON
            $jsonData = json_encode($data);

            // return JSON
            return $jsonData;
        }
	}
