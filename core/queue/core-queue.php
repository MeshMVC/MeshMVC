<?php
	namespace MeshMVC;
	
	require_once(PATH.'core/lib/phpquery/phpQuery-onefile.php');
	require_once(PATH."core/lib/topsort/vendor/autoload.php");

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

        public static function parse($from="", $function_output="", $filter="", $to="", $display_type="html", $display_mode="append", $use_models=true, $recursion_level=0) {

            require PATH . "core/queue/parser/recursion-safe.php";
            $recursion_level++;

            $model = null;
            if ($use_models) $model = \MeshMVC\Models::getAll();

            $processed_output = "";

            // no view template specified
            if ($from == "") throw new \Exception("No view template specified!");

            // TODO: add options to download via FTP, S3, etc
            // view is string or file
            if (substr($from, 0, 7)=='http://' || substr($from, 0, 8)=='https://') {

                // fetch url content into output
                try {
                    $fetch = \MeshMVC\Tools::download($from);
                } catch (Exception $e) {
                    // TODO: custom callback option
                    $fetch = false;
                }
                if ($fetch !== false) {
                    $processed_output = $fetch;
                } else {
                    // couldn't fetch url
                    throw new \Exception("Couldn't fetch URL: ".$from);
                }

            } else {

                // find all views
                $paths_to_load = [];
                foreach (\MeshMVC\Environment::$SEEDS as $dir) {
                    [$seed_type, $seeded_path] = explode(":", $dir);
                    if ($seed_type === "view") {
                        $paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($seeded_path));
                    }
                }
                $paths_to_load = array_unique($paths_to_load);

                // look for exact filename match
                $foundExactMatch = false;
                foreach ($paths_to_load as $possibleViewMatchFilename) {
                    if ($possibleViewMatchFilename == $from) {
                        $foundExactMatch = true;
                        $processed_output = file_get_contents($from);
                        break;
                    }
                }

                // on fail, look for basename match
                if ($foundExactMatch == false) {
                    $foundBaseMatch = false;
                    foreach ($paths_to_load as $possibleViewMatchFilename) {
                        if (basename($possibleViewMatchFilename) == $from) {
                            $foundBaseMatch = true;
                            $processed_output = file_get_contents($possibleViewMatchFilename);
                            break;
                        }
                    }

                    if ($foundBaseMatch == false) {
                        throw new \Exception("No local view file found for: ".$from."\nTIP: for remote files use 'HTTP' prefix.");
                    }
                }
            }

            if ($use_models && count($model) > 0) {
                ob_start();
                eval("?>" . $return_output . "<?php");
                $processed_output = ob_get_clean();

                if (\MeshMVC\Environment::DEBUG) {
                    foreach (self::$resources as $this_resource) {
                        $this_resource_filename = $this_resource["file"];
                        $this_resource_region = $this_resource["region"];
                    }
                }
            }

            if ($processed_output !== "" && $filter !== "") {
                $place_me = \phpQuery::newDocumentHTML($processed_output)[$filter]->html();
            } else {
                $place_me = $processed_output;
            }

            if ($to == "") {
                // override all previous templates if no target specified
                return $place_me;
            }

            //  get outerHTML
            $destination = \phpQuery::newDocumentHTML($function_output);
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

            // using wrapper hack to get outerHTML
            return $destination;
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
                        \MeshMVC\Cross::$currentController = $controller;
                        $do_unit_test = $controller->test();
                        if (!$controller->unit_tests_passed()) {
                            // controller unit tests failed
                            $unit_tests_failed = true;
                        } else {
                            // controller unit tests passed
                        }
                    } else {
                        // controller has no unit tests
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
                    \MeshMVC\Cross::$currentController = $controller;
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
                    // find controller of class name
                    $controller = $this->obj_controllers[$cname];
                    $controller->index = $controller_index++;

                    // execute if executable is found
                    if (method_exists($controller, 'run')) {
                        \MeshMVC\Cross::$currentController = $controller;
                        $controller->run();
                        // or render each views
                        foreach ($controller->loaded_views as $view) {
                            if ($view->doRenderOnDestruct) {
                                self::$complete_output = Queue::parse($view->from, self::$complete_output, $view->filter, $view->to, $view->display_type, $view->display_mode, $view->use_models, 0);
                            }
                        }
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