<?php

// TODO: fix apps with namespaces

// HTML skeleton controller:
class _html extends \MeshMVC\Controller {
    function sign() {
        return route("/html/*");
    }
    function run() {
        view("html.html");
    }
}

// TODO: render models as json for api access

class Page extends \MeshMVC\Model {
    public static $title = "MeshMVC";
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
        ->to("html body"); // appends page contents to document body
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
    }

}

class SimpleModel extends \MeshMVC\Model {
    public $test1 = "value1";
    public $tes2 = 2;

    public function test3() {
        return "executed function";
    }

    public function test4() {
        return 1 + 3;
    }

}

class _models_test extends \MeshMVC\Controller {

    function sign() {
        return route("/myapi/test"); // _html controller class dependency
    }
    function run() {
        echo models()
            ->add("test", new SimpleModel())
            ->add("page", new Page())
            ->json();
    }

}
