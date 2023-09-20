<?php

	namespace MeshMVC;

	class Config {

		// define if debugging or not
		public const DEBUG = true;
		
		// define environment
		public const ENV = "dev";

        public const SEEDS = array(
            "controller:webapp/packages/*.php",
            "css:webapp/packages/*.css",
            "js:webapp/packages/*.js",
            "media:webapp/packages/*.*"
        );

	}
?>