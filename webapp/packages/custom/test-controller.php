<?php

/* PHP Files can contain multiple controllers. */


// Homepage controller:
class _home extends \MeshMVC\Controller {
    function validate() {
echo "<3>";
        return route("/home") && needs("_page_components"); // _html controller class dependency
    }
    function execute() {
        view("home.html")
        ->to("html body"); // appends page contents to document body
    }
}
// HTML skeleton controller:
class _html extends \MeshMVC\Controller {
    function validate() {
echo "<1>";
        return route("/*");
    }
    function execute() {
        view("html.html");
    }
}

// Page components controller to ensure controllers fire in order
// (multiple controllers can fire for the same page/route/api)
class _page_components extends \MeshMVC\Controller {
    function validate() {
echo "<2>";
        return route("/*") && needs("_html");
    }
    function execute() {
        // multiple views can trigger per controller
        view("title.html")
        ->to("html body header"); // appends to body header element

        view("title.html")
        ->to("html body header"); // appends to body header element
    }
}
