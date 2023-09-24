<?php

	namespace MeshMVC;

	class Environment extends \MeshMVC\Config {

		public const SITE_NAME = "MeshMVC(".self::ENV.")";

		// REQUIRED: define if debugging or not
		public const DEBUG = true;

	    public const DB_NAME = "";
	    public const DB_HOST = "";
	    public const DB_USER = "";
	    public const DB_PASS = "";

	    public const AWS_S3_KEY = "";

        public const LOG_FILE = PATH."logs/notes.log";

        // REQUIRED: directories search patterns (can be modified)
        public static $SEEDS = array(
            // views search pattern
            "views:webapp/*.*",
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