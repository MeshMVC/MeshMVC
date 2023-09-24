<?php
	namespace MeshMVC;
	
	require_once(PATH.'core/lib/phpquery/phpQuery-onefile.php');
	require_once(PATH."core/lib/topsort/vendor/autoload.php");

	class Stats {
		public static $logs = Array(); 		
	}
	
	class Queue {
		public static $complete_output = "";

		private $obj_controllers = []; // Controllers Found
		private static $resources = Array(); // All stacked data
		
        public function __construct() {
            // Get the count of PHP's core declared classes
            $system_classes_count = count(get_declared_classes());

            // Initialize an array to store paths to load
            $paths_to_load = [];

            // Search for all files within the seeded directories
            foreach (\MeshMVC\Environment::$SEEDS as $dir) {
                [$seed_type, $seeded_path] = explode(":", $dir);
                if ($seed_type === "controller") {
                    $paths_to_load = array_merge($paths_to_load, Tools::search_files($seeded_path));
                }
            }

            // Remove duplicates from the paths to load
            $paths_to_load = array_unique($paths_to_load);

            // Include each path
            foreach ($paths_to_load as $path) {
                if ($path) {
                    include $path;
                }
            }

            // Get the custom classes by slicing the declared classes array
            $custom_classes = array_slice(get_declared_classes(), $system_classes_count);

            // Instantiate each controller found (identified by class extended from Controller)
            foreach ($custom_classes as $class) {
                if (in_array('MeshMVC\Controller', class_parents($class), true)) {
                    $this->obj_controllers[$class] = new $class;
                }
            }
        }

        public static function parse($from="", $currentOutput="", $filter="", $to="", $display_type="html", $display_mode="append", $use_models=true, $recursion_level=0) {
            require PATH . "core/queue/parser/recursion-safe.php";
            $recursion_level++;

            $model = \MeshMVC\Models::getAll();
            $stack = [];

            require PATH . "core/queue/parser/fetch-type.php";

            foreach ($stack as $this_output) {
                if ($use_models && count($model) > 0) {
                    ob_start();
                    eval("?>" . $this_output . "<?php");
                    $this_output = ob_get_clean();

                    if (\MeshMVC\Environment::DEBUG) {
                        $i = 0;
                        foreach (self::$resources as $this_resource) {
                            $this_resource_filename = $this_resource["file"];
                            $this_resource_region = $this_resource["region"];
                            $i++;
                            Models()->add("stats.resource_" . $i, "File: " . $this_resource_filename . ", Region: " . $this_resource_region);
                        }
                    }
                }

                if ($this_output !== "" && $filter !== "") {
                    $place_me = \phpQuery::newDocumentHTML($this_output)[$filter];
                } else {
                    $place_me = $this_output;
                }

                $destination = \phpQuery::newDocumentHTML($currentOutput);

                if ($to === "") {
                    // override all previous templates if no target specified
                    $currentOutput = $place_me;
                } else {
                    $content = ($display_type === "html") ? $place_me : htmlentities($place_me);

                    switch ($display_mode) {
                        case "prepend":
                            $destination[$to]->prepend($content);
                            break;
                        case "append":
                            $destination[$to]->append($content);
                            break;
                        case "replace":
                            $destination[$to]->replaceWith($content);
                            break;
                        case "inner":
                            $destination[$to]->html($content);
                            break;
                        default:
                            $destination[$to]->append($content);
                    }

                    return  $destination->html();
                }
            }
        }

        public static function output()
        {
            $paths_to_load = [];

            $doc = \phpQuery::newDocumentHTML(self::$complete_output);
            $headed = $doc["html head"]->length;

            if ($headed) {
                foreach (\MeshMVC\Environment::$SEEDS as $dir) {
                    [$seed_type, $seeded_path] = explode(":", $dir);

                    if ($seed_type === "css" || $seed_type === "js") {
                        $paths_to_load = array_merge($paths_to_load, Tools::search_files($seeded_path));
                    }
                }

                $paths_to_load = array_unique($paths_to_load);

                foreach ($paths_to_load as $resource) {
                    if (file_exists($resource)) {
                        $ext = pathinfo($resource, PATHINFO_EXTENSION);
                        $append_header = '';

                        if ($ext === "css") {
                            $append_header = '<link href="' . $resource . '" rel="stylesheet" />';
                        } elseif ($ext === "js") {
                            $append_header = '<script src="' . $resource . '"></script>';
                        }

                        $doc["html head"]->append($append_header);
                    }
                }

                self::$complete_output = $doc;
            }

            return self::$complete_output;
        }


        /* Main process thread */
        public function process()
        {
            // validate and assign priorities to controllers
            $priority_controllers = [];

            // ensure all unit tests pass when debugging
            if (\MeshMVC\Environment::DEBUG) {
                $unit_tests_failed = false;
                foreach ($this->obj_controllers as $cname => $controller) {
                    if (method_exists($controller, 'test')) {
                        $do_unit_test = $controller->test();
                        if (!$controller->unit_tests_passed()) {
                            // Stats::$logs[] = "unit_test_" . ($i + 1) . $cname . " fail";
                            $unit_tests_failed = true;
                        } else {
                            // Stats::$logs[] = "unit_test_" . ($i + 1) . $cname . " pass";
                        }
                    } else {
                        // Stats::$logs[] = "unit_test_" . ($i + 1) . $cname . " has no unit tests";
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
                if (method_exists($controller, 'sign')) {
                    $validator_priority = $controller->sign();
                    if ($validator_priority !== false) {
                        // when controller validates
                        $validator_priority = is_numeric($validator_priority) ? $validator_priority : 0;

                        $priority_controllers[$cname] = $validator_priority;
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
            $invalid_controllers = [];
            foreach ($priority_controllers as $this_controller_name => $priority_c) {
                \MeshMVC\Tools::dig_dependencies($this_controller_name, [], $this->obj_controllers, $invalid_controllers);
            }

            // remove invalid controllers
            foreach ($invalid_controllers as $this_controller_name => $error_message) {
                unset($priority_controllers[$this_controller_name]);
            }

            // output invalid controller in debug mode
            // debug($invalid_controllers);

            // do topological sort
            $sorter = new \MJS\TopSort\Implementations\StringSort();
            foreach ($priority_controllers as $this_controller_name => $priority_c) {
                $sorter->add($this_controller_name, $this->obj_controllers[$this_controller_name]->needed_controllers);
            }
            $controllers_sorted = $sorter->sort();

            // index of controllers
            $controller_index = 0;

            // set complete output
            self::$complete_output = "";

            foreach ($controllers_sorted as $i => $cname) {
                $cname = trim($cname);
                if ($cname !== "") {
                    // log controller
                    if (\MeshMVC\Environment::DEBUG) {
                        Stats::$logs[] = "controller_" . ($i + 1) . $cname;
                    }

                    // find controller of class name
                    $controller = $this->obj_controllers[$cname];
                    $controller->index = $controller_index++;

                    // execute if executable is found
                    if (method_exists($controller, 'run')) {
                        \MeshMVC\Cross::$currentController = $controller;
                        $controller->run();
                    }
                }
            }

            echo self::output();
        }
	
	}

	// Run Core
	$core = new Queue();
	$core->process();
?>