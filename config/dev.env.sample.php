<?php

	namespace MeshMVC;

	class Environment extends \MeshMVC\Config {

		public static $site_name = "MeshMVC";

		// REQUIRED: define if debugging or not
		public const DEBUG = true;

        // app namespace
        public const APP_NAMESPACE = "myapp";

        // download settings
	    public const DEFAULT_PROXY_AGENT = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	    public const DEFAULT_PROXY_TIMEOUT_MS = null; // no timeouts by default
	    public const DEFAULT_PROXY_VALIDATE_RESPONSE_CODES = true; // returns an error when response code isn't 200 (OK)

        public $storage = [
            "myS3" => new \MeshMVC\S3(),
            "mySFTP" => new \MeshMVC\SFTP(),
        ];

	    public const DB_NAME = "";

	    public const AWS_S3_KEY = "";

        public const LOG_FILE = PATH."logs/notes.log";

        // REQUIRED: directories search patterns (can be modified)
        public static $SEEDS = array(
            // plugins search pattern
            "plugin:webapp/packages/plugins/*.php",
            // views search pattern
            "view:webapp/*.*",
            // controllers search pattern
            "controller:webapp/packages/custom/*.php",
            // css auto-discovery search pattern
            "css:webapp/*.css",
            // js auto-discovery search pattern
            "js:webapp/*.js",
            // media files search pattern
            "media:webapp/*.*"
        );

        public function __construct() {
            self::$site_name = self::$site_name ." (".self::ENV.")";
        }

	}

?>