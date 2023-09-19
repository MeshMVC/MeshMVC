<?php

namespace MeshMVC; // see "Defining Namespaces" section

	class Tools {

		public static function version() {
			return file_get_contents("/build.version");
		}

		// test if current user has access level
		public static function access($access_level) {
			// TODO
			$user = new \MeshMVC\User();
			return $user->access($access_level);
		}

		// q(number) = query argument (starts with 0)
		// q(string) = test if current url matches string 
		public static function queryURL($arg_number='all', $controller=null, $dependencies=[]) {
			if ($arg_number=='all') {
				return (isset($_GET['q'])) ? $_GET['q'] : '/';
			} elseif (is_numeric($arg_number)) {
				$array = explode('/', $_GET['q']);
				if (isset($array[$arg_number])) {
					return $array[$arg_number.''];
				}
			} elseif (!is_numeric($arg_number)) {

                if ($controller != null) {
                    \MeshMVC\Router::$controllers_list[]  = [$controller, $dependencies]
                }

				return self::inpath($arg_number);
			}
			return '';
		}

		
		// q(string) = test if current url matches string 
		private static function inpath($url) {
			$q = isset($_GET['q']) ? $_GET['q'] : "";
			return fnmatch($url, "/" . $q);
		}

		// controller dependency manager, returns empty array when no children are found 
		public static function dig_dependencies($dep, $trail, &$objs,  &$invalid_controllers) {

			// add trail
			$trail[] = $dep;

			// detect infinite loop
			$freqs = array_count_values($trail);
			$freq_dep = $freqs[$dep];

			if ($freq_dep >= 2) {				
				foreach($trail as $invalid_controller) {
					$invalid_controllers[$invalid_controller] = "Caught controller: ".$invalid_controller." in a loop (circular controller dependencies). Trail cancelled: ".implode(", ", $trail);
				}
				return Array();
			}

			//  not found (invalid node)
			if (!isset($objs[$dep])) {
				foreach($trail as $invalid_controller) {
					$invalid_controllers[$invalid_controller] = "Controller '".$dep."' not found. Trail cancelled: ".implode(", ", $trail);
				}
				return Array();
			}

			$controller = $objs[$dep];
			$dep_children = $controller->needed_controllers;

			if (count($dep_children) > 0) {
				foreach ($dep_children as $dependency) {
					$ret_dump = self::dig_dependencies($dependency, $trail, $objs, $invalid_controllers);
				}
				return Array();
			} else {
				// when no child found, stop recursion
				return Array();
			}
		}


		//custom sort 
		public static function prioritySorter($a, $b) {
			if ($a == $b) return 0;
			return ($a > $b) ? 1 : -1;
		}


		public static function Posted($var) {
			if (isset($_POST[$var])) {
				return $_POST[$var];
			}
			return "";
		}

		public static function Got($var) {
			if (isset($_POST[$var])) {
				return true;
			} else {
				return false;
			}
		}

		// write to logs
		public static function note($data) {
			$log_file = PATH."logs/notes.log";
			$fh = @fopen($log_file, 'a');
			@fwrite($fh, $data."\n");
			@fclose($fh);
		}

		// validate email
		public static function validate_email($email) {
			$isValid = true;
			$atIndex = strrpos($email, "@");
			if (is_bool($atIndex) && !$atIndex) {
				$isValid = false;
			} else {
				$domain = substr($email, $atIndex+1);
				$local = substr($email, 0, $atIndex);
				$localLen = strlen($local);
				$domainLen = strlen($domain);
				if ($localLen < 1 || $localLen > 64) {
					// local part length exceeded
					$isValid = false;
				} else if ($domainLen < 1 || $domainLen > 255) {
					// domain part length exceeded
					$isValid = false;
				} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
					// local part starts or ends with '.'
					$isValid = false;
				} else if (preg_match('/\\.\\./', $local)) {
					// local part has two consecutive dots
					$isValid = false;
				} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
					// character not valid in domain part
					$isValid = false;
				} else if (preg_match('/\\.\\./', $domain)) {
					// domain part has two consecutive dots
					$isValid = false;
				} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
					// character not valid in local part unless 
					// local part is quoted
					if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
						$isValid = false;
					}
				}
				if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
					// domain not found in DNS
					$isValid = false;
				}
			}
			return $isValid;
		}

		// redirect user to new location
		public static function redirect($url) {
			header("Location: ". $url);
			die();
		}

		// translate text
		public static function translate($text = "") {
			// on empty string, return current language code
			if ($text == "") return $this->lang;
			return $text;
		}
		
		// send email
		public static function email($to, $subject, $message) {
			// normal headers
			$num = md5(time()); 
			$headers  = "From: ShiftSmith <noreply@".$_SERVER["SERVER_NAME"].".com>\r\n";

			// This two steps to help avoid spam   
			$headers .= "Message-ID: <".time()." TheSystem@".$_SERVER['SERVER_NAME'].">\r\n";
			$headers .= "X-Mailer: PHP v".phpversion()."\r\n";         

			// With message
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

			mail($to, $subject, $message, $headers);
		}

		public static function endsWith($haystack, $needle) {
			$length = strlen($needle);
			if ($length == 0) {
				return true;
			}

			return (substr($haystack, -$length) === $needle);
		}


		public static function search_files($pattern, $flags = 0) {
			$files = glob($pattern, $flags); 
			foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
				$files = array_merge(
					[],
					...[$files, self::search_files($dir . "/" . basename($pattern), $flags)]
				);
			}
			return $files;
		}

		private static function search_files_matching_helper($filename, $dir, &$results = array()){
			$files = glob($dir);

			foreach($files as $current_file) {
				$path = realpath($filename);
				if(!is_dir($path)) {
					if ( (strtolower($filename)) == (strtolower(basename($path))) ) {
						$results[] = $path;
					} elseif ( fnmatch(strtolower(basename($filename)), strtolower(basename($path))) ) {
						$results[] = $path;
					}
				} else if($value != "." && $value != "..") {
					// on directory, browse
					self::search_files_matching_helper($filename, $path, $results);
				}
			}
			return $results;
	 	}

		public static function search_files_matching($filename, $dir){

			$results = self::search_files_matching_helper($filename, $dir);

			return $results;
		}
		
		public static function mvc_input($input_type, $var, $val) {
			switch ($input_type) {
				case "radio":
					return '<input type="radio" name="'.$var.'" value="'.$val.'" />';
					break;
				case "textarea":
					return '<textarea name="'.$var.'">'.$val.'</textarea>';
					break;
				case "checkbox":
					return '<input type="checkbox" name="'.$var.'" value="'.$val.'" />';
					break;
				case "password":
					// passwords values aren't returned for security reasons
					return '<input type="password" name="'.$var.'" />';
					break;
				default:
					return '<input type="textbox" name="'.$var.'" value="'.$val.'" />';
					// textbox
			}
		}
			
	}
?>
