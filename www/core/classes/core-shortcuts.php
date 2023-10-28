<?php
	/* This file contains shortcuts to functions and classes within namespaces */

    function debug($obj) {
        if (\MeshMVC\Environment::DEBUG) {
            echo "<pre style='color: #333; font-family: source-code-pro, Menlo, Monaco, Consolas, 'Courier New', monospace; font-size:14px;'>";
            var_dump($obj);
            echo "</pre>";
        }
    }

	// query access
	function a($access_required) {
		return \MeshMVC\Tools::access($access_required);
	}
	function access($access_required) {
		return \MeshMVC\Tools::access($access_required);
	}
	
	
	// query url
	function q($argNumber_or_inPathString) {
		return \MeshMVC\Tools::queryURL($argNumber_or_inPathString);
	}

	/**
	 * Determines if current request is within a url
	 * @link https://meshmvc.com/????
	 * @param mixed $url <p>
	 * The searched url pattern.
	 * </p>
	 * @return bool true if within route
	 * false otherwise.
	 */
	function route($url) {
		return \MeshMVC\Tools::queryURL($url);
	}

    /**
     * gets input parameter from BODY post as json
     * @link https://meshmvc.com/????
     * @param string $var <p>
     * The searched variable, when null returns all parameters
     * </p>
     * @return mixed
     * false otherwise.
     */
    function input($var = null) {
        return \MeshMVC\Tools::input($var);
    }

    // query url
	function t($translate_string_id) {
		return \MeshMVC\Tools::translate($translate_string_id);
	}

	/**
	 * Creates a view
	 * @link https://meshmvc.com/????
	 * @param mixed $object <p>
	 * Filename, basename or url of template to be fetched, OR Model instance for APIs.
	 * </p>
	 * @return \MeshMVC\View returns current View (Chainable with Fluent Interface)
	 */
	function view($type) {
        foreach (\MeshMVC\Cross::$viewTypes as $ktype => $class) {
            if (strtolower($type) == strtolower($ktype)) {
                \MeshMVC\Cross::$currentController->addView(new $class);
            }
        }

		return \MeshMVC\Cross::$currentView;
	}

	/**
	 * Attaches one or more controllers to be loaded before this one as a dependency
	 * @link https://meshmvc.com/????
	 * @param mixed $controller_list <p>
	 * The controller name(s)
	 * </p>
	 * <p>
	 * If controller_list is a string, a single controller is added as a dependency..
	 * </p>
	 * <p>
	 *  If controller_list is an array of strings, each controller is added as a dependency..
	 *  </p>         *
	 * @return \MeshMVC\Controller returns current controller (Chainable with Fluent Interface)
	 */
	function needs($controllers) {
	    $current_this = \MeshMVC\Cross::$currentController;
		@$current_this->needs($controllers);
		return $current_this;
	}

	/**
	 * Test the current request method, i.e. "POST", "GET"
	 * @link https://meshmvc.com/????
	 * @param string $request_method <p>
	 *
	 * </p>
	 * @return bool returns true when request method matches the parameter
	 */
	function method($request_method) {
		return \MeshMVC\Tools::method($request_method);
	}

	/**
	 * Adds a model to the queue or fetch a model (when no value is set)
	 * @link https://meshmvc.com/????
	 * @param mixed $name <p>
	 * Name of object for internal and external reference.
	 * </p>
	 * @param mixed $instance <p>
	 *  Set Model instanced value object for internal and external reference.
	 *  </p>
	 * 	@return \MeshMVC\Model returns current Model
	 */
	function model($name, $instance = null) {
		// when no instance defined
		if ($instance == null) {
			// return model
			return \MeshMVC\Models::get($name);
		}

		// add new model by default
		@\MeshMVC\Cross::$models->add($name, $instance);
	}

	// Model Group obj
	function models() {
		return new \MeshMVC\Models();
	}
	function group() {
		return new \MeshMVC\Models();
	}
	function table() {
		return new \MeshMVC\Models();
	}
	function matrix() {
		return new \MeshMVC\Models();
	}

	// Database wrapper
	function db($query) {
		return new \MeshMVC\Database($query);
	}

	function posted($arg) {
		return \MeshMVC\Tools::Posted($arg);
	}
	function got($arg) {
		return \MeshMVC\Tools::Got($arg);
	}
	function redirect($url) {
		return \MeshMVC\Tools::redirect($url);
	}

