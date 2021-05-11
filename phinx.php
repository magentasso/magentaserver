<?php

use MagentaServer\Application;
\MagentaServer\Application::loadConfiguration();

const DSN_KEY_TRANSLATIONS = [
	'driver' => 'adapter',
	'database' => 'name',
	'username' => 'user',
	'password' => 'pass',
];

function dsn_to_environment(string $dsn): array {
	$env = \MagentaServer\Helpers\DatabaseCapsule::parse_dsn($dsn);
	foreach ($env as $key => $value) {
		if (array_key_exists($key, DSN_KEY_TRANSLATIONS)) {
			$env[DSN_KEY_TRANSLATIONS[$key]] = $value;
			unset($env[$key]);
		}
	}

	// If SQLite: set blank suffix
	if ($env['adapter'] === 'sqlite') {
		$env['suffix'] = '';
	}

	return $env;
}

return [
	'version_order' => 'creation',
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
		'development' => dsn_to_environment($_ENV['DATABASE_DSN']),
    ],
];
