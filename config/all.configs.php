<?php

	namespace MeshMVC;

	class Config {

		// define if debugging or not
		public const DEBUG = true;
		
		// define environment
		public const ENV = "dev";

        public static $SEEDS = array(
            "controller:webapp/*.php",
            "css:webapp/*.css",
            "js:webapp/*.js",
            "media:webapp/*.*"
        );

	}
?>