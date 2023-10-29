<?php

namespace MeshMVC\StorageTypes;

use MeshMVC\Storage;

class Local extends \MeshMVC\Storage {

    public function __construct() {
        return $this;
    }

    // required by abstract class
    public function connect() : self {
        // TODO: (optional) mount in File System?
        // do nothing.
        return $this;
    }

    // required by abstract class
    public function disconnect() : self {
        // TODO: (optional) unmount in File System?
        // do nothing.
        return $this;
    }

    public function upload($location, $data, $operation="") : self {
        $this->performance_start();
        if ($operation == "append") {
            $operation = FILE_APPEND;
        } else {
            $operation = 0;
        }

        file_put_contents($location, $data, $operation);
        $this->performance_end();
        return $this;
    }

    public function download($location) : mixed {
        $this->prefix_download($location);
        $this->performance_start();
        $output = file_get_contents($location, true);
        $this->performance_end();
        return $output;
    }

    public function query($query) : mixed {
        return $this->download($query);
    }

    function bulk_start(): \MeshMVC\Storage {
        // TODO: (optional) transaction start
        throw new \Exception("No rollback possible.");
    }

    function bulk_end(): \MeshMVC\Storage {
        // TODO: (optional) transaction end: revert if anything fails in between.
        throw new \Exception("No rollback possible.");
    }
}
