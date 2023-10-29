<?php

namespace MeshMVC;

abstract class Storage {

    private $performance = null;
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

    public function performance() {
        return $this->performance;
    }
    public function performance_start() : self {
        $this->performance = microtime(true);
        return $this;
    }

    public function performance_end() : self {
        $this->performance = microtime(true) - $this->performance;
        return $this;
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

    public function export($storage_alias, $location) {
        file_put_contents($location, \MeshMVC\Cross::storage($storage_alias)->download());
    }

    public static function prefix_download(&$url) {
        if (str_starts_with($url, "/")) {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            $baseUrl = $protocol . $domainName;
            $url = $baseUrl.$url;
        }
    }

}
