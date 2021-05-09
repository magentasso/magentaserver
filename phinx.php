<?php

use MagentaServer\Application;
\MagentaServer\Application::loadConfiguration();

return [
	'version_order' => 'creation',
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
		'development' => [
			'dsn' => $_ENV['DATABASE_URL'],
		],
    ],
];
