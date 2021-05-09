<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

\MagentaServer\Application::loadConfiguration();
\MagentaServer\Application::createApp()->run();
