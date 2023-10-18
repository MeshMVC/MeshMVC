<?php

use \JmesPath\Env as JmesPath;

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
