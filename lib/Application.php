<?php
namespace MagentaServer;

use MagentaServer\Container;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

class Application {
	public static function loadConfiguration(): void {
		$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
		$dotenv->load();
		$dotenv->required('ENVIRONMENT')->allowedValues(['development', 'production']);
		$dotenv->required('DATABASE_URL');
		$dotenv->required('REDIS_URL');
		$dotenv->required('SITE_TITLE');
		$dotenv->required('SITE_SESSIONCOOKIE');

		$i18n = new \i18n(dirname(__DIR__) . '/lang/{LANGUAGE}.ini', dirname(__DIR__) . '/cache/lang', 'en');
		$i18n->init();
	}

	public static function createApp(): \Slim\App {
		// Create and register the container
		AppFactory::setContainer(new Container());

		// Create the app
		$app = AppFactory::create();
		$app->add(TwigMiddleware::createFromContainer($app));
		$app->addRoutingMiddleware();
		$app->addErrorMiddleware(true, true, true);

		// Register routes
		$app->any("/", Controllers\IndexController::class)->setName('index');

		// Return the created app
		return $app;
	}
}
