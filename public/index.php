<?php
declare(strict_types=1);
require_once(dirname(dirname(($p = realpath(__FILE__)) === false ? __FILE__ : $p)) . '/vendor/autoload.php');

\MagentaServer\Application::createApp()->run();
