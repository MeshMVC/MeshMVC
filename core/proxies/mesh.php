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
        return end($this->performance)["timed"];
    }

    // TODO: add __before__[function_name] hooks
    public function __call($name, $args) {

        if ($_ENV["config"]["debug"]) $outer_start = microtime(true);

        if (property_exists($this->class, "__before__call")) $this->class->__before__call();

        if ($_ENV["config"]["debug"]) $start = microtime(true);

        $ret = $this->class->$name(...$args);

        if ($_ENV["config"]["debug"]) $end = microtime(true);

        if (property_exists($this->class, "__after__call")) $this->class->__after__call();

        if ($_ENV["config"]["debug"]) $outer_end = microtime(true);

        if ($_ENV["config"]["debug"]) $this->performance[] = ["function"=>$name, "start"=>$start, "end"=>$end, "timed" => $end-$start, "outer_timed"=>$outer_end-$outer_start ];

        return $ret;
    }

    public function __get($name) {
        return $this->class->$name;
    }

    public function __set($name, $value) {
        $this->class->$name = $value;
    }

}
