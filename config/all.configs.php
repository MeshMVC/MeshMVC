<?php

	namespace MeshMVC;

	class Config {

		// REQUIRED: define if debugging or not
		public const DEBUG = true;
		
		// OPTIONAL: define environment to be used with environment file
		// i.e. "dev" would look for dev.env.php in the config (this) directory
		public const ENV = "dev";

        // REQUIRED: directories search patterns
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