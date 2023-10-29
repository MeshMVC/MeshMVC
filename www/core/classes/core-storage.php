<?php

namespace MeshMVC;

abstract class Storage {

    private mixed $link = null;
    private mixed $bulk_failed = false;

    abstract function connect() : self;
    abstract function disconnect() : self;

    abstract function upload($location, $data, $operation = "write") : self; // on fail: throw error
    abstract function download($location) : mixed;

    abstract function bulk_start() : self;
    abstract function query($query) : mixed;
    abstract function bulk_end() : self;

    public function __contruct() {
        $args = func_get_args();
        return $this->connect(...$args);
    }

    public function link($link = null) : mixed {
        if (empty($link)) return $this->link;
        $this->link = $link;
        return $this;
    }

    public function bulk_failed($status = null) : mixed {
        if (empty($status)) return $this->bulk_failed;
        $this->bulk_failed = $status;
        return $this;
    }

}
