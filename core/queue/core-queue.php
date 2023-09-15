<?php
	namespace MeshMVC;
	
	require_once(PATH.'core/lib/phpquery/phpQuery-onefile.php');
	require_once(PATH."core/lib/topsort/vendor/autoload.php");

	class Stats {
		public static $logs = Array(); 		
	}
	
	class Queue {
		private static $complete_output = "";

		private $obj_controllers; // Controllers Found
		private static $resources = Array(); // All stacked data
		
		public function __construct() {
			// Get php's core declared classes count
			$system_classes_count = count(get_declared_classes());
			
			$paths_to_load = array();

			// search for all files within the seeded directories
			$initial_dirs = explode(",", SEEDS);
			foreach ($initial_dirs as $dir) {
				$dir = explode(":", $dir);
				$seed_type = $dir[0];
				$seeded_path = $dir[1];
				if ($seed_type == "controller") {
					$paths_to_load = array_merge($paths_to_load, Tools::search_files($seeded_path));
				}
			}
			$paths_to_load = array_unique($paths_to_load);

			foreach ($paths_to_load as $path) {
				if ($path) include $path;
			}
			
			// Remove all classes of php's core to identify webapp classes
			$custom_classes = array_slice(get_declared_classes(), $system_classes_count);

			// Instantiate each controller found (identified by class extended of Controller)
			foreach($custom_classes as $class) {
				if (in_array('MeshMVC\Controller', class_parents($class))) {
					$this->obj_controllers[$class] = new $class;
				}
			}

		}

		public static function parse($from='', $ret_type='render', $filter="", $to="", $display_type="html", $display_mode="replace_inner", $use_models=true,  $recursion_level=0) {

			// var_dump($from, $ret_type, $filter, $to, $display_type, $display_mode, $use_models,  $recursion_level);
			// prevent infinite recursions
			require PATH."core/queue/parser/recursion-safe.php";			
			$recursion_level++;

			$model = \MeshMVC\Models::getAll();
			$this_output = "";
			$stack = array();

			// start output buffer
			//if (!Config::DEBUG) ob_start();
		
			// get view as filename, url or data
			require PATH."core/queue/parser/fetch-type.php";

			foreach ($stack as $this_output) {

				if ($use_models && count($model) > 0) {

					// THE DANGEROUS MIGHTY FUNCTION OF ALL HEAVENS & HELL

					ob_start();
					echo eval("?>$this_output<?");
					$this_output = ob_get_contents();
					ob_end_clean();

					//debug output resources
					$i = 0;
					foreach (self::$resources as $this_resource) {
						$this_resource_filename = $this_resource["file"];
						$this_resource_region = $this_resource["region"];
						if (\MeshMVC\Config::DEBUG) {
							$i++;
							Models()->add("stats.resource_".$i, "File: ".$this_resource_filename.", Region: ".$this_resource_region);
						}
					}
				}

				// get source HTML
				$place_me = $this_output;
				if ( ($this_output != "") && ($filter != "") ) {
					$place_me = \phpQuery::newDocumentHTML($this_output)[$filter];
				} 

				// set destination HTML
				$destination = \phpQuery::newDocumentHTML(self::$complete_output);
				if ($to == "") {
					self::$complete_output = $place_me;
				} else {		
					// replace_inner, prepend, append, replace, replace_inner
					switch ($display_mode) {
						case "prepend":
							if ($display_type == "html") {
								$destination[$to]->prepend($place_me);
							} else {
								$destination[$to]->prepend(htmlentities($place_me));
							}
							break;
						case "append":
							if ($display_type == "html") {
								$destination[$to]->append($place_me);
							} else {
								$destination[$to]->append(htmlentities($place_me));
							}
							break;
						case "replace":
							if ($display_type == "html") {
								$destination[$to]->replaceWith($place_me);
							} else {
								$destination[$to]->replaceWith(htmlentities($place_me));
							}
							break;
						default: // replace_inner
							if ($display_type == "html") {
								$destination[$to]->html($place_me);
							} else {
								$destination[$to]->text($place_me);
							}
					}
					self::$complete_output = $destination;
				}
			}
		}

		public static function output() {
			
			// get resources: auto add CSS & JS Files if in HTML context
			$paths_to_load = array();

			// search for all files within the seeded directories
			$doc = \phpQuery::newDocumentHTML(self::$complete_output);
			$headed = $doc["html head"]->length;
			if ($headed) {
				$initial_dirs = explode(",", SEEDS);
				foreach ($initial_dirs as $dir) {
					$dir = explode(":", $dir);
					$seed_type = $dir[0];
					$seeded_path = $dir[1];
					if ($seed_type == "css" || $seed_type == "js" ) {
						$paths_to_load = array_merge($paths_to_load, Tools::search_files($seeded_path));
					}
				}
				$paths_to_load = array_unique($paths_to_load);
 
				foreach($paths_to_load as $resource) {
					if (@file_exists($resource)) {
						$arr = explode('.', $resource);
						$ext = end($arr);
						switch ($ext) {
							case "css":
								$append_header = '<link href="'.$resource.'" rel="stylesheet" />';
								break;
							case "js":
								$append_header = '<script src="'.$resource.'"></script>';
								break;
						}
						$doc["html head"]->append($append_header);
					}
				}
				self::$complete_output = $doc;
			}

			// get current output
			return self::$complete_output;
		}

		/* Main process thread */
		public function process() {

			// validate and assign priorities to controllers
			$priority_controllers = array();

			// ensure all unit tests pass when debugging
			if (Config::DEBUG) {
				$i = 0;
				$unit_tests_failed = false;
				foreach ($this->obj_controllers as $cname => $controller) {
					$i++;
					if (method_exists($controller, 'test')) {
						$do_unit_test = $controller->test();
						if (!$controller->unit_tests_passed()) {
							Stats::$logs[] = "unit_test_".$i. $cname." fail";
							$unit_tests_failed = true;
						} else {
							Stats::$logs[] = "unit_test_".$i.$cname." pass";
						}
					} else {
						Stats::$logs[] = "unit_test_".$i. $cname." has no unit tests";
					}
				}
 
				// logging happens in controllers
				if ($unit_tests_failed) {
					echo "UNIT TEST(S) FAILED!";
					die();
				}
			}

			// ensure controllers validate when a validation function is found
			foreach ($this->obj_controllers as $cname => $controller) {
				if (method_exists($controller, 'validate')) {
					$validator_priority = $controller->validate();
					if ($validator_priority !== false) {
						// when controller validates
						if (!is_numeric($validator_priority)) $validator_priority = 0; // when priority isn't numeric assign zero value
						
						$priority_controllers[$cname] = $validator_priority;	// assign priority to unique class name
					} else {
						// free resources and invalidate obj
						$this->obj_controllers[$cname] = null;
					}
				} else {
					$this->obj_controllers[$cname] = null;
				}
			}

			// sort controllers by highest priority to lowest
			uasort($priority_controllers, 'MeshMVC\Tools::prioritySorter');

			// prevent infinite loops & dependencies to controllers that don't exist
			$invalid_controllers = Array();
			foreach ($priority_controllers as $this_controller_name => $priority_c) {
				$digger = \MeshMVC\Tools::dig_dependencies($this_controller_name, Array(), $this->obj_controllers, $invalid_controllers);
			}

			// remove invalid controllers
			foreach ($invalid_controllers as $this_controller_name => $error_message) {
				if (isset($priority_controllers[$this_controller_name])) {
					unset($priority_controllers[$this_controller_name]);
				}
			}

			// do topological sort
			$sorter = new \MJS\TopSort\Implementations\StringSort();
			foreach ($priority_controllers as $this_controller_name => $priority_c) {
				$sorter->add($this_controller_name, $this->obj_controllers[$this_controller_name]->needed_controllers);
			}
			$controllers_sorted = $sorter->sort();

			// index of controllers
			$controller_index = 0;

			$i = 0;
			foreach ($controllers_sorted as $cname) {
				if (trim($cname) != "") {
					// log controller
					if (\MeshMVC\Config::DEBUG) {
						$i++;
						Stats::$logs[] = "controller_".$i.$cname;
					}

					// find controller of class name
					$controller = $this->obj_controllers[$cname];
					$controller->index = $controller_index;
					$controller_index++;

					// execute if executable is found
					if (method_exists($controller, 'execute')) $controller->execute();
				}
			}

			echo self::output();

		}
	
	}
	// Run Core
	$core = new Queue();
	$core->process();
?>