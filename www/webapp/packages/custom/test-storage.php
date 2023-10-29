<?php

namespace myapp;

class _Storage_Test extends \MeshMVC\Controller {

    // test with POST body: {"query": "query { echo(message: \"Hello World\") }" }
    // or: {"query": "mutation { sum(x: 2, y: 3) }" }
    function sign() {
        return route("/storage");
    }

    function run() {

        storage("mylocal")
            ->upload("myfile.txt", "test-output");

        debug(storage("mylocal")
            ->download("myfile.txt"));

    }
}
