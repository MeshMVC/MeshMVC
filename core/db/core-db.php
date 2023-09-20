<?php
	namespace MeshMVC;

	class Database {
		static private $dbtype;
		static private $dblink;
		static private $isConnected;
		static private $counted = 0;

		public function __construct() {
			if (defined("DB_HOST") && defined("DB_USER") && defined("DB_PASS") && defined("DB_NAME")) {
				$con = self::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                $args = func_get_args();
				if (count($args) > 0) {
				    return self::query();
				} else {
				    return $con;
				}
			}
			return false;
		}

		static public function connect($host, $user, $password, $database, $dbtype='mysql') {
			
			self::$isConnected = false;

			try {
				self::$dbtype = $dbtype;

				if (self::$dbtype=='mysql') {
					if ($host=="") return false;
					self::$dblink = @new \mysqli($host, $user, $password, $database);
					if (!isset(self::$dblink->connect_error)) self::$isConnected = true;
				}

				return self::$dblink;
			
			} catch (Exception $e) {

				return false;
				
			}
			return self::$dblink;
		}

		static public function isConnected() {
			return self::$isConnected;
		}

		static function query() {
            $args = func_get_args();
            return self::queryWithArgs($args);
        }

		// query(SQL: ? for parameters, "idsb" (int, double, string, blob), arg1, arg2);
		static function queryWithArgs($args) {
			try {
				// init vars
				$values = array();
				$query = array_shift($args);
				$stmt =  self::$dblink->stmt_init();

				// log sql query when in debug mode
				if (\MeshMVC\Config::DEBUG) {
					self::$counted++;
					Model("sql_".self::$counted, str_replace("\t","",$query), "stats");
					Model("sql_params".self::$counted, implode(", ", $args), "stats");
				}

				// ex: INSERT INTO CountryLanguage VALUES (?, ?);
				if ($stmt->prepare($query)) {
					if (count($args) > 0) {
						$params_config = array_shift($args);

						// secure parameters into SQL query statement with unpacking parameters(...)
						$stmt->bind_param($params_config, ...$args);
					}
				}

				// execute SQL query
				@$stmt->execute();
				$ret = array();
				$results = @$stmt->get_result();
				while (($results != null) && ($row = $results->fetch_array(MYSQLI_BOTH))) {
					$ret[] = $row;
				}
				return $ret;
			} catch (Exception $e) {
				return false;
			}
		}

		static private function clean($string) {
			$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
			return preg_replace('/[^A-Za-z0-9_]/', '', $string); // Removes special chars.
		}

		static function write($rows, $ns, $defs) {
			$ns = self::clean($ns);
			$sql = "INSERT INTO ".$ns." (";
			$cols = array();
			$types = "";
			foreach($defs as $def) {
				foreach($def as $col => $type) {
					$cols[] = $col;
					$types .= $type;
				}
			}
			$sql .= implode(",", $cols);
			$sql .= ") VALUES (";
			
			$params = array();
			foreach ($rows as $r) {
				$params[] = "?";
			}
			$sql .= implode(",", $params);
			
			$sql .= ");";

			self::query($sql, $types, ...$rows);
		}
		static function setTable($namespace, $defs) {
			$sql = "CREATE TABLE ".$namespace." (";
			$sqlq = array();
			
			//"idsb" (int, double, string, blob)
			// special: T : text
			foreach($defs as $def) {
				foreach($def as $col => $type) {
					switch (substr($type,0,1)) {
						case "i": // integer
							$sqlq[] = $col." INT(11)";
							break;
						case "d": // double
							$sqlq[] = $col." INT(11)";
							break;
						case "b": // boolean
							$sqlq[] = $col." BOOL";
							break;
						case "t": // string
							$sqlq[] = $col." TEXT";
							break;
						default: // string
							$sqlq[] = $col." VARCHAR(255)";
					}
					
				}
			}
			$sql .= implode(",", $sqlq); 
			$sql .=	")";

			self::query($sql);
		}
	}
?>
