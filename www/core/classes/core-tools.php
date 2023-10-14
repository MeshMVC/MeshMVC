<?php

namespace MeshMVC;
use \JmesPath\Env as JmesPath;

	class Tools
    {

        public static function version()
        {
            return file_get_contents("/build.version");
        }

        // test if current user has access level
        public static function access($access_level)
        {
            // TODO
            $user = new \MeshMVC\User();
            return $user->access($access_level);
        }

        public static function download($url, $proxy = null)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, \MeshMVC\Environment::DEFAULT_PROXY_AGENT);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            if (\MeshMVC\Environment::DEFAULT_PROXY_TIMEOUT_MS != null) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, \MeshMVC\Environment::DEFAULT_PROXY_TIMEOUT_MS);
            }

            $output = curl_exec($ch);

            /*
            // get proxy errors
            if (curl_getinfo($ch, CURLINFO_PROXY_ERROR) != CURLPX_OK) {
                throw new \Exception("Proxy(".CURLOPT_PROXY.") error downloading: ".$url);
            }
            */

            // get response code to ensure
            if (\MeshMVC\Environment::DEFAULT_PROXY_VALIDATE_RESPONSE_CODES) {
                $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                if ($code != 200) throw new \Exception($code . " error downloading: " . $url);
            }

            curl_close($ch);
            return $output;
        }

        // q(number) = query argument (starts with 0)
        // q(string) = test if current url matches string
        public static function queryURL($arg_number = 'all', $controller = null, $dependencies = [])
        {
            if ($arg_number === 'all') {
                return $_GET['q'] ?? '/';
            } elseif (is_numeric($arg_number)) {
                $array = explode('/', $_GET['q']);
                return $array[$arg_number] ?? '';
            } elseif ($controller !== null) {
                \MeshMVC\Router::$controllers_list[] = [$controller, $dependencies];
            }

            return self::inpath($arg_number);
        }


        // q(string) = test if current url matches string
        private static function inpath($url)
        {
            $q = $_GET['q'] ?? "";
            return fnmatch($url, "/" . $q);
        }

        // controller dependency manager, returns empty array when no children are found
        // $this_controller_name, Array(), $this->obj_controllers, $invalid_controllers
        public static function dig_dependencies($dep, $trail, &$objs, &$invalid_controllers)
        {

            // Add trail
            $trail[] = $dep;

            // Detect infinite loop
            $freq_dep = array_count_values($trail)[$dep] ?? 0;

            // Handle infinite loop
            if ($freq_dep >= 2) {
                foreach ($trail as $invalid_controller) {
                    $invalid_controllers[$invalid_controller] = "Caught controller: {$invalid_controller} in a loop (circular controller dependencies). Trail cancelled: " . implode(", ", $trail);
                }
                return [];
            }

            // Handle invalid node
            if (!isset($objs[$dep])) {
                foreach ($trail as $invalid_controller) {
                    $invalid_controllers[$invalid_controller] = "Controller '{$dep}' not found. Trail cancelled: " . implode(", ", $trail);
                }
                return [];
            }

            // Check each child dependency for infinite loops and invalid nodes
            $dep_children = $objs[$dep]->needed_controllers;
            foreach ($dep_children as $dependency) {
                self::dig_dependencies($dependency, $trail, $objs, $invalid_controllers);
            }

            return [];
        }


        // custom sort
        public static function prioritySorter($a, $b)
        {
            return $a <=> $b;
        }


        public static function Posted($var)
        {
            return $_POST[$var] ?? "";
        }

        public static function Got($var)
        {
            return isset($_POST[$var]);
        }

        // write to logs
        public static function note($data)
        {
            $log_file = \MeshMVC\Environment::LOG_FILE;
            if ($fh = fopen($log_file, 'a')) {
                fwrite($fh, $data . PHP_EOL);
                fclose($fh);
            }
        }

        // validate email
        public static function validate_email($email)
        {
            $isValid = true;
            $atIndex = strrpos($email, "@");

            if (false === $atIndex) {
                return false;
            }

            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);

            if ($localLen < 1 || $localLen > 64
                || $domainLen < 1 || $domainLen > 255
                || $local[0] === '.' || $local[$localLen - 1] === '.'
                || preg_match('/\\.\\./', $local)
                || !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)
                || preg_match('/\\.\\./', $domain)
                || !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))
            ) {
                return false;
            }

            if (!(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                return false;
            }

            return true;
        }

        // redirect user to new location
        public static function redirect($url)
        {
            header("Location: " . $url);
            die();
        }

        // translate text
        public static function translate($text = "")
        {
            // on empty string, return current language code
            return $text;
        }

        public static function email($to, $subject, $message)
        {
            $from = \MeshMVC\Environment::SITE_NAME . " <noreply@" . $_SERVER["SERVER_NAME"] . ".com>";

            $headers = array(
                "From" => $from,
                "Message-ID" => "<" . time() . "." . uniqid() . "@" . $_SERVER['SERVER_NAME'] . ">",
                "X-Mailer" => "PHP v" . phpversion(),
                "MIME-Version" => "1.0",
                "Content-Type" => "text/html; charset=UTF-8",
                "Reply-To" => $from,
                "Return-Path" => $from,
                "X-Priority" => "3",
                "X-MSMail-Priority" => "Normal",
                "X-Mailgun-Native-Send" => "true"
            );

            $header_lines = [];
            foreach ($headers as $key => $value) {
                $header_lines[] = $key . ": " . $value;
            }

            $header_string = implode("\r\n", $header_lines);

            $success = mail($to, $subject, $message, $header_string);

            if (!$success) {
                return false;
            }

            return true;
        }

        public static function endsWith($haystack, $needle)
        {
            $length = strlen($needle);
            if ($length === 0) {
                return true;
            }

            return substr($haystack, -$length) === $needle;
        }

        public static function search_files($pattern, $flags = 0)
        {
            $files = glob($pattern, $flags);

            foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
                $files = array_merge($files, self::search_files($dir . '/' . basename($pattern), $flags));
            }

            return $files;
        }

        private static function search_files_matching_helper($filename, $dir, &$results = [])
        {
            $files = glob($dir);

            foreach ($files as $current_file) {
                if (!is_dir($current_file)) {
                    $current_filename = strtolower(basename($current_file));
                    $search_filename = strtolower(basename($filename));

                    if ($current_filename === $search_filename || fnmatch($search_filename, $current_filename)) {
                        $results[] = realpath($current_file);
                    }
                } elseif ($current_file !== "." && $current_file !== "..") {
                    self::search_files_matching_helper($filename, $current_file, $results);
                }
            }

            return $results;
        }

        public static function search_files_matching($filename, $dir)
        {
            $results = self::search_files_matching_helper($filename, $dir);
            return $results;
        }

        public static function mvc_input($input_type, $var, $val)
        {
            switch ($input_type) {
                case "radio":
                    return '<input type="radio" name="' . $var . '" value="' . $val . '" />';
                    break;
                case "textarea":
                    return '<textarea name="' . $var . '">' . $val . '</textarea>';
                    break;
                case "checkbox":
                    return '<input type="checkbox" name="' . $var . '" value="' . $val . '" />';
                    break;
                case "password":
                    // passwords values aren't returned for security reasons
                    return '<input type="password" name="' . $var . '" />';
                    break;
                default:
                    return '<input type="textbox" name="' . $var . '" value="' . $val . '" />';
                // textbox
            }
        }

        public static function jsonDecode($json) {
            if (empty($json)) throw new \Exception("Empty json!");
            return json_decode($json, true);
        }

        public static function jsonEncode($json) {
            if (empty($json)) throw new \Exception("Empty json!");
            return json_encode($json, JSON_PRETTY_PRINT);
        }

        public static function jsonRemoveMatching(string $json, string $selector): string {
            // Decode the JSON string to an associative array
            $jsonData = json_decode($json, true);

            // Create a new array to hold the modified data
            $modifiedData = [];

            // Iterate over the original data
            foreach ($jsonData as $key => $value) {
                // Use JMESPath to check if the current item matches the selector
                $result = JmesPath::search($selector, $value);

                // If the result is not null, it means the current item matches the selector.
                // In this case, we skip this item and do not add it to the modified data.
                if ($result !== null) {
                    continue;
                }

                // Otherwise, we add this item to the modified data
                $modifiedData[$key] = $value;
            }

            // Convert the modified data back to a JSON string
            $modifiedJson = json_encode($modifiedData);

            // Return the modified JSON
            return $modifiedJson;
        }


        public static function jsonAppend(string $json, string $selector, $newData): string {
            // Decode the JSON string to an associative array
            $jsonData = json_decode($json, true);

            // Create a new array
            $modifiedData = [];

            // Iterate over the original data
            foreach ($jsonData as $key => $value) {
                // Use JMESPath to check if the current item matches the selector
                $result = JmesPath::search($selector, $value);

                // If the result is not null, it means the current item matches the selector.
                // In this case, we add the new data before this item.
                if ($result !== null) {
                    $modifiedData = array_merge($modifiedData, $newData);
                }

                $modifiedData[$key] = $value;
            }

            // Convert the modified data back to a JSON string
            $modifiedJson = json_encode($modifiedData);

            // Return the modified JSON
            return $modifiedJson;
        }

        public static function jsonPrepend(string $json, string $selector, $newData): string {
            // Decode the JSON string to an associative array
            $jsonData = json_decode($json, true);

            // Create a new array
            $modifiedData = [];

            // Iterate over the original data
            foreach ($jsonData as $key => $value) {
                // Use JMESPath to check if the current item matches the selector
                $result = JmesPath::search($selector, $value);

                // If the result is not null, it means the current item matches the selector.
                // In this case, we prepend the new data before this item.
                if ($result !== null) {
                    $modifiedData = array_merge($newData, $modifiedData);
                }

                $modifiedData[$key] = $value;
            }

            // Convert the modified data back to a JSON string
            $modifiedJson = json_encode($modifiedData);

            // Return the modified JSON
            return $modifiedJson;
        }

        public static function jsonMerge(string $json, string $selector, $newData): string {
            // Decode the JSON string to an associative array
            $jsonData = json_decode($json, true);

            // Iterate over the original data
            foreach ($jsonData as $key => &$value) {
                // Use JMESPath to check if the current item matches the selector
                $result = JmesPath::search($selector, $value);

                // If the result is not null, it means the current item matches the selector.
                // In this case, we merge the new data with the current item.
                if ($result !== null) {
                    if (is_array($value) && is_array($newData)) {
                        $value = array_merge($value, $newData);
                    }
                }
            }

            // Convert the modified data back to a JSON string
            $modifiedJson = json_encode($jsonData);

            // Return the modified JSON
            return $modifiedJson;
        }

        public static function jsonReplace(string $json, string $selector, $newData): string {
            // Decode the JSON string to an associative array
            $jsonData = json_decode($json, true);

            // Iterate over the original data
            foreach ($jsonData as $key => &$value) {
                // Use JMESPath to check if the current item matches the selector
                $result = JmesPath::search($selector, $value);

                // If the result is not null, it means the current item matches the selector.
                // In this case, we replace the current item with the new data.
                if ($result !== null) {
                    $value = $newData;
                }
            }

            // Convert the modified data back to a JSON string
            $modifiedJson = json_encode($jsonData);

            // Return the modified JSON
            return $modifiedJson;
        }

    }
