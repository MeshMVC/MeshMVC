<?php

namespace MeshMVC\StorageTypes;

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

        $directory_path = dirname($location);

        // Check if the directory does not exist.
        if (!is_dir($directory_path)) {
            // Attempt to create the directory.
            if (!mkdir($directory_path, 0755, true)) {
                throw new \Exception("Failed to create directories for file to write at: $location");
            }
        }


        if ($operation == "append") {
            $operation = FILE_APPEND;
        } else {
            $operation = 0;
        }

        file_put_contents($location, $data, $operation);
        return $this;
    }

    public function download($location) : mixed {
        $this->prefix_download($location);
        $output = file_get_contents($location, true);
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

    function cache($key, $value): mixed {
        // TODO: cache with .mo & .po files
    }

}
