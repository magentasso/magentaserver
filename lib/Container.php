<?php
namespace MagentaServer;

class Container extends \DI\Container {
	public function __construct() {
		parent::__construct();

		// Set up Twig view helper
		$this->set('view', function() {
			if (empty($userTemplateDir = trim($_ENV['SITE_TEMPLATEDIR'] ?? ""))) $userTemplateDir = null;
			$templateDirs = array_filter([$userTemplateDir, dirname(__DIR__) . '/templates']);

			$twig = \Slim\Views\Twig::create($templateDirs, [
				'cache' => dirname(__DIR__) . '/cache/templates',
				'debug' => ($_ENV['ENVIRONMENT'] == 'development'),
			]);

			$twig->addExtension(new \MagentaServer\Helpers\TwigExtension());
			return $twig;
		});
	}
}
