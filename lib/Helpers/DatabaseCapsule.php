<?php
namespace MagentaServer\Helpers;

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseCapsule {
	private static $instance = null;

	public static function get(): Capsule {
		if (self::$instance === null) {
			// Parse the DATABASE_URL environment variable
			$options = self::parse_url($_ENV['DATABASE_URL']);

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
	public static function parse_url(string $url): array {
		$url = parse_url($url);

		$scheme = empty($scheme = rtrim($url['scheme'] ?? '', ':/')) ? null : $scheme;
		$host = empty($host = trim($url['host'] ?? '')) ? null : $host;
		$user = empty($user = trim($url['user'] ?? '')) ? null : $user;
		$pass = empty($pass = trim($url['pass'] ?? '')) ? null : $pass;
		$path = empty($path = ltrim($url['path'] ?? '', '/')) ? null : $path;

		return array_filter([
			'driver' => $scheme,
			'host' => $host,
			'database' => $path,
			'username' => $user,
			'password' => $pass,
		]);
	}
}
