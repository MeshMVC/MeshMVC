<?php

class SimpleModel extends \MeshMVC\Model {
    public $zzz = "zzz";
    public $test1 = "value1";
    public $test2 = 2;
    private $counter = 0;

    public function test3() {
        return "executed function";
    }

    public function test4() {
        return $this->countup();
    }

    public function countup() {
        return ++$this->counter;
    }
}

class _models_test extends \MeshMVC\Controller {

    function sign() {
        return route("/json/test"); // _html controller class dependency
    }

    function run() {
        /*
        $json = '{ "zzz": "zzz", "test1": "value1", "test2": 2, "test3": "executed function", "test4": 4 }';
        echo "<h4>1:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json); // should output  { "zzz": "zzz", "test1": "value1", "test2": 2, "test3": "executed function", "test4": 4 }
        echo "<h4>2:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "zzz"); // should output  {zzz: "zzz"}
        echo "<h4>3:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "zzz", "XXX"); // should output  {zzz: "XXX"}
        echo "<h4>4:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "*", null, "test1"); // should output  { "zzz": "zzz", "test2": 2, "test3": "executed function", "test4": 4 }
        echo "<h4>5:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "*", "XXX", "test1"); // should output  { "zzz": "XXX", "test2": "XXX", "test3": "XXX", "test4": "XXX" }
        $json = '{ "zzz": "zzz", "test1": "value1", "test2": 2, "test3": "executed function", "test4": 4 }';
        echo "<h4>6:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "*", '{"x": "Y"}', "test1"); // should output { "zzz": {"x": "Y"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"} }
        $json = '{ "zzz": {"x": "Y"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"} }';
        echo "<h4>7:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "zzz.x"); // should output {x: "Y"}
        $json = '[{"zzz": {"x": "Y"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"}}, {"q": 22}]';
        echo "<h4>8:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "1.q"); // should output  {q: 22}
        echo "<h4>9:</h4>";
        echo \MeshMVC\Tools::jsonSelector($json, "0.zzz"); // should output  {x: "Y"}

        // patterns example:
        $json = '[{"zzz": {"x": "Y"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"}}, {"q": 22}]';
        echo "<h4>10:</h4>";
        // PATTERN MATCHING NOT WORKING!
        // use jsonpath: softcreatr/jsonpath
        echo \MeshMVC\Tools::jsonSelector($json, "0.z*"); // should output: {"zzz": {"x": "Y"}}

        die();
        */

        view(new SimpleModel()) // filter properties and methods for selected
        ->trim("test3"); // filter to remove properties and methods for selected

        //->to("test[0].property1");

        // output would be automatic
        //->by("append"); // default merge

        /*
        view(new Page())
            ->trim("user_id") // filter to remove properties and methods for selected
            ->to("test1")
            ->by("append");
        */
    }
}


class _unit_tests extends \MeshMVC\Controller {

    function sign() {
        return route("/json/unit"); // _html controller class dependency
    }

    function run() {

        $json = '{ "zzz": "zzz", "test1": "value1", "test2": 2, "test3": "executed function", "test4": 4 }';
        echo "1:". \MeshMVC\Tools::jsonRemoveMatching($json, "test3")."\n";
        // should output: 1: { "zzz": "zzz", "test1": "value1", "test2": 2, "test4": 4 }
        echo "2:". \MeshMVC\Tools::jsonRemoveMatching($json, "test*")."\n";
        // should output: 2: { "zzz": "zzz"}
        echo "3:". \MeshMVC\Tools::jsonRemoveMatching($json, "*est3")."\n";
        // should output: 3: { "zzz": "zzz", "test1": "value1", "test2": 2, "test4": 4 }
        $json = '{ "zzz": {"x": "Y", "z": "Z"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"} }';
        echo "4:". \MeshMVC\Tools::jsonRemoveMatching($json, "zzz.x")."\n";
        // should output: 4: { "zzz": {"z": "Z"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"} }
        echo "5:". \MeshMVC\Tools::jsonRemoveMatching($json, "zzz.*")."\n";
        // should output: 5: { "zzz": {"z": "Z"}, "test2": {"x": "Y"}, "test3": {"x": "Y"}, "test4": {"x": "Y"} }
    }
}
