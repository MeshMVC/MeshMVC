<?php

namespace myapp;

class _Storage_Test extends \MeshMVC\Controller {

    function sign() {
        return route("/storage");
    }

    function run() {

        storage("sftp")
            ->upload("myfile.txt", "test-output");

        debug(storage("mylocal")
            ->download("myfile.txt"));

    }
}
