<?php

namespace MeshMVC;

class CoreConfig {

    public static $storage = [];

    public static function addStorage($id, $storageConfig) {
        $newView = new \MeshMVC\Cross::$storageTypes[$id]();
        $newView->connect($storageConfig);
        \MeshMVC\Cross::$currentController->addView($newView);
        return self::class;
    }

}
