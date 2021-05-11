<?php
namespace MagentaServer\Helpers;

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseCapsule {
	const DSN_KEY_TRANSLATIONS = [
		'dbname' => 'database',
	];
	
	private static $instance = null;

	public static function get(): Capsule {
		if (self::$instance === null) {
			// Parse the DATABASE_DSN environment variable
			$options = self::parse_dsn($_ENV['DATABASE_DSN']);

			// Connect to the database
			self::$instance = new Capsule();
			self::$instance->addConnection($options);
			self::$instance->setAsGlobal();
			self::$instance->bootEloquent();
		}

		return self::$instance;
	}
	
	/**
	 * @return array<string, mixed> 
     */
	public static function parse_dsn(string $dsn): array {
		list($driver, $params) = explode(":", $dsn, 2);
		$params = array_map('trim', explode(";", $params));

		$options = [];
		foreach ($params as $param) {
			if (strpos($param, '=') === false) {
				if ($driver === 'sqlite') {
					$options['database'] = trim($param);
				} else {
					trigger_error("DSN contained non-key-value option", E_USER_WARNING);
				}
			} else {
				list($key, $value) = array_map('trim', explode("=", $param, 2));
				if (array_key_exists($key, self::DSN_KEY_TRANSLATIONS)) {
					$options[self::DSN_KEY_TRANSLATIONS[$key]] = $value;
					unset($options[$key]);
				} else {
					$options[$key] = $value;
				}
			}
		}

		return array_merge(array_filter($options), ['driver' => $driver]);
	}
}
