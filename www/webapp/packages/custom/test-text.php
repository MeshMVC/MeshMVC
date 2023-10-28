<?php

class _text_test extends \MeshMVC\Controller {

    function sign() {
        return route("/text"); // _html controller class dependency
    }

    function run() {
        view("text")
            ->from("Super tests from space from everywhere.")
            ->filter('/from space/') // filter to remove properties and methods for selected
            ->trim('/from/'); // filter to remove properties and methods for selected

        view("text")
            ->from("Huzzah Append")
            ->by("append")
            ->to('/sp/');

        view("text")
            ->from("Huzzah Prepend")
            ->by("prepend")
            ->to('/sphuz/i');

        view("text")
            ->from("replaced")
            ->by("replace")
            ->to('/p/i');

    }
}