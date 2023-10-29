<?php

namespace MeshMVC;
use \MeshMVC\Views;
use \MeshMVC\StorageTypes;

    // used for global shortcuts to access current controllers and views
	class Cross {
	    public static $currentController = null;
	    public static $currentView = null;
        public static $models = null;

        public static $storage = [];

        // TODO: automate these values
        public static $viewTypes = [
            "gql" => Views\Gql::class,
            "html" => Views\Html::class,
            "json" => Views\Json::class,
            "text" => Views\Text::class,
            "openapi" => Views\OpenAPI::class,
        ];

        // TODO: automate these values
        public static $storageTypes = [
            "curl" => StorageTypes\Curl::class,
            "local" => StorageTypes\Local::class,
            "mysql" => StorageTypes\MySQL::class,
            "s3" => StorageTypes\S3::class,
            "sftp" => StorageTypes\SFTP::class,
            "zip" => StorageTypes\Zip::class,
        ];

        public function __construct() {
            self::$models = new \MeshMVC\Models();
        }

        public static function storage($alias, $instance = null) {
            if ($alias === "all") return self::$storage;
            if (empty($instance)) return self::$storage[$alias];
            self::$storage[$alias] = $instance;
            return $instance;
        }
    }