<?php

foreach (self::$resources as $filename) {

	$this_resource_filename = $filename["file"];
	$this_resource_region = $filename["region"];

	$place_me = '';
	$i=0;
	$ext = substr(strrchr($filename['file'],'.'), 1);

	switch ($ext) {
		case "js":
			$place_me = '<script type="text/javascript" src="'.$this_resource_filename.'"></script>'."\n";
			break;
		case "css":
			$place_me = '<link rel="stylesheet" type="text/css" media="all" href="'.$this_resource_filename.'" />'."\n";
			break;
		default:
			$place_me = '';
			break;
	}

	if ($place_me != "") {

		if (\MeshMVC\Config::DEBUG) {
			$i++;
			Model("resource_".$i, "File: ".$this_resource_filename.", Region: ".$this_resource_region, "stats");
		}

		// set destination HTML
		$destination = qp($this_output);
		$region = $this_resource_region;
		$destination[$region]->append($place_me);
		$this_output = $destination;
	}
}