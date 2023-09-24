<?php

	namespace MeshMVC;

	class Environment extends \MeshMVC\Config {

		public const SITE_NAME = "MeshMVC(".self::ENV.")";

		// REQUIRED: define if debugging or not
		public const DEBUG = true;

        // download settings
	    public const DEFAULT_PROXY_AGENT = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	    public const DEFAULT_PROXY_TIMEOUT_MS = null; // no timeouts by default
	    public const DEFAULT_PROXY_VALIDATE_RESPONSE_CODES = true; // returns an error when response code isn't 200 (OK)

	    public const DB_NAME = "";
	    public const DB_HOST = "";
	    public const DB_USER = "";
	    public const DB_PASS = "";

	    public const AWS_S3_KEY = "";

        public const LOG_FILE = PATH."logs/notes.log";

        // REQUIRED: directories search patterns (can be modified)
        public static $SEEDS = array(
            // views search pattern
            "view:webapp/*.*",
            // controllers search pattern
            "controller:webapp/*.php",
            // css auto-discovery search pattern
            "css:webapp/*.css",
            // js auto-discovery search pattern
            "js:webapp/*.js",
            // media files search pattern
            "media:webapp/*.*"
        );

	}

?>