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
    }

    // required by abstract class
    public function disconnect() : self {
        // TODO: (optional) unmount in File System?
        throw new \Exception("No connection possible.");
    }

    public function upload($location, $data, $operation="") : self {

        if ($operation == "append") {
            $operation = FILE_APPEND;
        } else {
            $operation = 0;
        }

        file_put_contents($location, $data, $operation);
        return $this;
    }

    public function download($location) : mixed {
        return file_get_contents($location);
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
