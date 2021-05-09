<?php
namespace MagentaServer\Helpers;

class RedisInstance {
	private static $instance = null;

	public static function get(): \Redis {
		if (self::$instance === null) {
			// Parse the REDIS_URL environment variable
			$url = parse_url($_ENV['REDIS_URL']);
			$host = $url['host'] ?? '127.0.0.1';
			$port = intval($url['port'] ?? 6379);
			$dbindex = intval(ltrim($url['path'], '/') ?? 0);

			// Connect to Redis
			self::$instance = new \Redis();
			self::$instance->connect($host, $port);
			self::$instance->select($dbindex);
		}

		return self::$instance;
	}
}