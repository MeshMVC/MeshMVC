<?php

	if ($from != '') {
		// view is string or file
		if (substr($from, 0, 4 )=='http') {
			// fetch url content into output
			$this_output = @file_get_contents($from);
		}

        $paths_to_load = [];
        foreach (\MeshMVC\Environment::$SEEDS as $dir) {
            list($seed_type, $seeded_path) = explode(':', $dir);
            $paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($seeded_path));
        }

        $paths_to_load = array_unique($paths_to_load);

        if (!empty($paths_to_load)) {
            $from_file = str_replace('./', '', $paths_to_load[0]);
            $stack = [file_get_contents($from_file)];
        }
	}
