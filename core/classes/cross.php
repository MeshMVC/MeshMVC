<?php

namespace MeshMVC;
use \MeshMVC\Views;
use \MeshMVC\StorageTypes;

    // used for global shortcuts to access current controllers and views
	class Cross {
	    public static $currentController = null;
	    public static $currentView = null;
        public static $models = null;
        public static $loggers = [];
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

        public static $loggerTypes = [
            "logger" => \MeshMVC\Loggers\Logger::class,
        ];

        public function __construct() {
            self::$models = new \MeshMVC\Models();
        }

        public static function storage($alias, $instance = null) {
            if (empty($instance)) return self::$storage[$alias];
            self::$storage[$alias] = new \MeshMVC\Mesh($instance);
            return self::$storage[$alias];
        }
        public static function storages() : array {
            return self::$storage;
        }

        public static function logger($alias, $instance = null) {
            if (empty($instance)) return self::$loggers[$alias];
            self::$loggers[$alias] = new \MeshMVC\Mesh($instance);
            return self::$loggers[$alias];
        }
        public static function loggers() : array {
            return self::$loggers;
        }
    }