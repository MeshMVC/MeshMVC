<?php

namespace MeshMVC;

    // used for global shortcuts to access current controllers and views
	class Cross {
	    public static $currentController = null;
	    public static $currentView = null;
        public static $models = null;

        public function __construct() {
            self::$models = new \MeshMVC\Models();
        }
    }