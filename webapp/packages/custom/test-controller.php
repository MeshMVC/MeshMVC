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

        view(new SimpleModel())
        ->filter("*test*") // filter properties and methods for selected
        ->trim("test3"); // filter to remove properties and methods for selected
        //->to("test[0].property1");
        // output would be automatic
        //->by("append"); // default merge

        view(new Page())
            ->filter("*") // filter properties and methods for selected
            ->trim("user_id*") // filter to remove properties and methods for selected
            ->to("test1");
    }
}

// need to add bindings between class methods and api