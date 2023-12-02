<?php


class _Storage_Test extends \MeshMVC\Controller {

    function sign() {
        return route("/storage");
    }

    function run() {

        // test curl
        debug("Download Performance:");

        $micro = storage("curl");
        $micro->download("https://luclaverdure.com/");
        debug("curl :".$micro->performance_pop());
        $micro = storage("fs");
        $micro->download("https://luclaverdure.com/");
        debug("fs :".$micro->performance_pop());

        // debug("Upload Performance:");
        // debug(storage("sftp")->upload("myfile.txt", "test-output")->performance());

        // debug("Compression Performance:");
        // debug(storage("zip")->upload("myfile.txt", "test-output")->performance());

    }
}
