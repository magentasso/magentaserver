<?php
namespace MagentaServer;

class Container extends \DI\Container {
	public function __construct() {
		parent::__construct();

		// Set up Twig view helper
		$this->set('view', function() {
		    $twig = \Slim\Views\Twig::create(dirname(__DIR__) . '/templates', [
				'cache' => dirname(__DIR__) . '/cache/templates',
				'debug' => ($_ENV['ENVIRONMENT'] == 'development'),
			]);
			
			$twig->addExtension(new \MagentaServer\Helpers\TwigExtension());
			return $twig;
		});
	}
}
