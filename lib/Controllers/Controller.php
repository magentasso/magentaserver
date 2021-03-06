<?php
namespace MagentaServer\Controllers;

use MagentaServer\Session\Session;
use MagentaServer\Session\EasyCSRFSessionProvider;
use EasyCSRF\EasyCSRF;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller {
	/** @var ContainerInterface $container */
	private $container;

	/** @var Session $session */
	public $session;

	/** @var EasyCSRF $csrf */
	public $csrf;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
		$this->session = new Session(null);
		$this->csrf = new EasyCSRF(new EasyCSRFSessionProvider($this->session));
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, ?array $args = []): ResponseInterface {
		$reqMethod = "request{$request->getMethod()}";
		if (!method_exists($this, $reqMethod)) {
			return $response->withStatus(405);
		}

		$cookies = $request->getCookieParams();
		if (array_key_exists($_ENV['SITE_SESSIONCOOKIE'], $cookies)) {
			$this->session->setSessionID($cookies[$_ENV['SITE_SESSIONCOOKIE']])->retrieve();
		}

		try {
			return $this->$reqMethod($request, $response, $args);	
		} catch (\EasyCSRF\Exceptions\InvalidCsrfTokenException $e) {
			return $this->renderView($request, $response, "errors/csrf.html");
		}
	}
	
	/**
	 * @param string $routeName Route name
     * @param array<string, string> $data Route placeholders
     * @param array<string, string> $queryParams Query parameters
	 * @return string 
	*/
	public function urlFor(string $routeName, array $data = [], array $queryParams = []): string {
		return $this->container
			->get('Slim\\App')
			->getRouteCollector()
			->getRouteParser()
			->urlFor($routeName, $data, $queryParams);
	}

	public function renderView(ServerRequestInterface $request, ResponseInterface $response, string $template, ?array $args = []): ResponseInterface {
		$args = array_merge([
			'site_title' => $_ENV['SITE_TITLE'] ?? 'Magenta',
			'site_environment' => $_ENV['APP_ENV'],
			'session' => $this->session->retrieve(),
			'user' => $this->session->isLoggedIn() ? $this->session->currentUser() : null,
			'csrf' => $this->csrf,
		], $args ?? []);

		return $this->container->get('view')->render($response, $template, $args);
	}
}
