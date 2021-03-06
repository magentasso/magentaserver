<?php
namespace MagentaServer;

use MagentaServer\Container;
use MagentaServer\Helpers\DatabaseCapsule;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

class Application {
	public static function loadConfiguration(): void {
		$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
		$dotenv->load();

		// Verify environment
		$dotenv->required('APP_ENV')->allowedValues(['development', 'test', 'production']);
		$dotenv->required('DATABASE_DSN')->notEmpty();
		$dotenv->required('REDIS_URL')->notEmpty();
		$dotenv->required('SITE_SESSIONCOOKIE')->notEmpty();
		$dotenv->ifPresent('SITE_TITLE')->notEmpty();
		$dotenv->ifPresent('SITE_TEMPLATEDIR')->notEmpty();
	}

	public static function loadLanguages(): void {
		// Load i18n code
		$i18n = new \i18n(dirname(__DIR__) . '/lang/{LANGUAGE}.ini', dirname(__DIR__) . '/cache/lang', 'en');
		$i18n->init();
	}

	public static function createApp(): \Slim\App {
		self::loadConfiguration();
		self::loadLanguages();
		DatabaseCapsule::get();

		// Create the container
		$container = new Container();

		// Create the app
		$app = \DI\Bridge\Slim\Bridge::create($container);
		$app->add(TwigMiddleware::createFromContainer($app));
		$app->addRoutingMiddleware();
		$app->addErrorMiddleware(true, true, true);

		// Register routes
		$app->any("/", Controllers\IndexController::class)->setName('index');
		$app->any("/auth/login", Controllers\AuthLoginController::class)->setName('auth-login');
		$app->any("/auth/logout", Controllers\AuthLogoutController::class)->setName('auth-logout');
		$app->get("/sso/magenta", Controllers\SSOMagentaController::class)->setName('sso-magenta');
		$app->any("/user", Controllers\UserSettingsController::class)->setName('usersettings');
		$app->any("/admin", Controllers\SiteAdminController::class)->setName('siteadmin');
		$app->any("/admin/clients", Controllers\SiteAdminClientController::class)->setName('siteadmin-client');
		$app->any("/admin/clients/create", Controllers\SiteAdminClientCreateController::class)->setName('siteadmin-clientcreate');

		if ($_ENV['APP_ENV'] === 'development') {
			$app->any("/debug", Controllers\DebuggingController::class)->setName('debug');
		}

		// Return the created app
		return $app;
	}
}
