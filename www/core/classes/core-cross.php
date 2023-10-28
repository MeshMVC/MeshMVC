<?php

namespace MeshMVC;

    // used for global shortcuts to access current controllers and views
	class Cross {
	    public static $currentController = null;
	    public static $currentView = null;
        public static $models = null;
        public static $viewTypes = [
            "gql" => \MeshMVC\Views\Gql::class,
            "html" => \MeshMVC\Views\Html::class,
            "json" => \MeshMVC\Views\Json::class,
            "text" => \MeshMVC\Views\Text::class,
        ];

        public function __construct() {
            self::$models = new \MeshMVC\Models();
        }
    }