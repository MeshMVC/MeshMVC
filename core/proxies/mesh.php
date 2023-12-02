<?php

namespace MeshMVC;

class Mesh {

    private $class = null;
    private $performance = [];

    public function __construct($class) {
        $this->class = $class;
        return $this->class;
    }

    public function performance() {
        return $this->performance;
    }

    public function performance_pop() {
        return end($this->performance)["time"];
    }

    public function __call($name, $args) {

        if ($_ENV["config"]["debug"]) {
            $start = microtime(true);
        }

        $ret = $this->class->$name(...$args);

        if ($_ENV["config"]["debug"]) {
            $end = microtime(true);
        }

        $this->performance[] = ["function" => $name, "time" => $end - $start ];

        return $ret;
    }

    public function __get($name) {
        return $this->class->$name;
    }

    public function __set($name, $value) {
        $this->class->$name = $value;
    }

}
