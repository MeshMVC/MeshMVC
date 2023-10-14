<?php

// TODO: fix apps with namespaces
// namespace namespace_test

// HTML skeleton controller:
class _html extends \MeshMVC\Controller {
    function sign() {
        return route("/html/*");
    }
    function run() {
        view("html.html");
    }
}

class Page extends \MeshMVC\Model {
    public static $title = "MeshMVC";
    public static $user_id = 0;
}

// Page components controller to ensure controllers fire in order
// (multiple controllers can fire for the same page/route/api)
class _page_components extends \MeshMVC\Controller {
    function sign() {
        return route("/html/*") && needs("_html");
    }
    function run() {
        model("page", new Page());

        view("title.html")
        ->to("html head"); // appends to body header element
    }
}

// Homepage controller:
class _home extends \MeshMVC\Controller {
    function sign() {
        return route("/html/home") && needs("_page_components"); // _html controller class dependency
    }
    function run() {
        view("home.html")
            ->to("#shitters"); // appends page contents to document body
    }
}

class _resume extends \MeshMVC\Controller {

    function sign() {
        return route("/html/resume") && needs("_page_components"); // _html controller class dependency
    }
    function run() {
        view("https://luclaverdure.com")
            ->filter("#history p:eq(0)")

            ->to("html body"); // appends page contents to document body
        // TODO: fix relative links
    }
}

class SimpleModel extends \MeshMVC\Model {
    public $zzz = "zzz";
    public $test1 = "value1";
    public $test2 = 2;

    public function test3() {
        return "executed function";
    }

    public function test4() {
        return 1 + 3;
    }
}

class _models_test extends \MeshMVC\Controller {

    function sign() {
        return route("/api/test"); // _html controller class dependency
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

        view(new SimpleModel())
        ->filter("test1"); // filter properties and methods for selected

        //->trim("test3"); // filter to remove properties and methods for selected

        //->to("test[0].property1");

        // output would be automatic
        //->by("append"); // default merge

        view(new Page())
            ->trim("user_id") // filter to remove properties and methods for selected
            ->to("test1");
    }
}
