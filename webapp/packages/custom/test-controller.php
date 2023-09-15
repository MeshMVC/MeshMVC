<?php

class Looper extends MeshMVC\Model {
    public static $items = Array();

    public static function add($row, $rnd) {
        $items[] = ["row" => $row, "random" => $rnd];
    }

    public function getItems() {
        return self::$items;
    }
}

class _html_looper extends \MeshMVC\Controller {
    function validate() {
        $this->needs("_html_title");
        return route("/test");
    }
    function execute() {
        $looper = new Looper();
        for ($i=0; $i <= 10; ++$i) {
            $looper::add($i, mt_rand(1,999));
        }

        Models()
        ->add("looper", $looper);

        View()
        ->from("looper.html")
        ->to("body")
        ->render("append", null); // runs once then caches result
    }
}

class Page extends MeshMVC\Model {
    public static $title = "Wizard.Build";
}

class _html_title extends \MeshMVC\Controller {
    function validate() {
        $this->needs("_html");
        return route("/test");
    }
    function execute() {
        // var_name, value, namespace
        $page = New Page();
        $page::$title = "Hello World";

        Models()
        ->add("page", $page);

        View()
        ->from("title.html")
        ->to("body")
        ->render("append", null); // null is to prevent caching
    }
}


class _html extends \MeshMVC\Controller {
    function validate() {
        return route("/test");
        echo "test";
    }
    function execute() {
        View()
        ->from("html.html")
        ->render();
    }
}

