<?php

	if ($from != '') {
		// view is string or file
		if (substr($from, 0, 4 )=='http') {
			// fetch url content into output
			$this_output = @file_get_contents($from);
		}

		$paths_to_load = array();
		foreach (\MeshMVC\Config::$SEEDS as $dir) {
			$dir = explode(":", $dir);
			$seed_type = $dir[0];
			$seeded_path = $dir[1];
			$paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($from));
			foreach($paths_to_load as $key => $tpl) {
				if (basename($from) != basename($tpl)) {
					unset($paths_to_load[$key]);
				}
			}
		}
		$paths_to_load = array_unique($paths_to_load);
		if (count($paths_to_load) > 0) {
			$from_file = str_replace("./", "", $paths_to_load[0]);
			$stack = array(@file_get_contents($from_file));
		}
	}
