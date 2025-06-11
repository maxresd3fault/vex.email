<?php
	// ©vex.email
	
	class DB {
		private static $instance = NULL;
		private function __construct() {
		}
		public static function getInstance() {
			
			if (!self::$instance) {
				self::$instance = new PDO("".DB_TYPE.":host=".LOCALHOST.";dbname=".DB_NAME."", DB_USERNAME, DB_PASSWORD);
				self::$instance->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
				self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			return self::$instance;
		}
		private function __clone() {
		}
	}