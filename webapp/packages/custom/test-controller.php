<?php

// TODO: fix apps with namespaces

// HTML skeleton controller:
class _html extends \MeshMVC\Controller {
    function sign() {
        return route("/*");
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
        return route("/*") && needs("_html");
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
        return route("/home") && needs("_page_components"); // _html controller class dependency
    }
    function run() {
        view("home.html")
        ->to("html body"); // appends page contents to document body
    }
}

class _resume extends \MeshMVC\Controller {

    function sign() {
        return route("/resume") && needs("_page_components"); // _html controller class dependency
    }
    function run() {
        $output = nl2br(view("https://luclaverdure.com/wp-content/uploads/CV-Luc-Laverdure-EN.txt")->toString());
        view($output)
            ->to("html body"); // appends page contents to document body
    }
}
