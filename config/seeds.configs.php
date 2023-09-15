<?php

// defines where to search for files
define('SEEDS', implode (',',
    array(
        "controller:core/packages/*.php",
        "controller:webapp/packages/*.php",
        "css:webapp/packages/*.css",
        "css:core/packages/*.css",
        "js:webapp/packages/*.js",
        "js:core/packages/*.js",
        "media:webapp/packages/*.*",
        "media:core/packages/*.*",
    )
));

?>