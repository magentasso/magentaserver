<?php
namespace MagentaServer\Controllers;

use MagentaServer\Session\Session;
use MagentaServer\Session\EasyCSRFSessionProvider;
use EasyCSRF\EasyCSRF;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller {
	private $container;
	public Session $session;
	public EasyCSRF $csrf;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
		$this->session = new Session(null);
		$this->csrf = new EasyCSRF(new EasyCSRFSessionProvider($this->session));
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		$reqMethod = "request{$request->getMethod()}";
		if (!method_exists($this, $reqMethod)) {
			return $response->withStatus(405);
		}

		$cookies = $request->getCookieParams();
		if (array_key_exists($_ENV['SITE_SESSIONCOOKIE'], $cookies)) {
			$this->session->setSessionID($cookies[$_ENV['SITE_SESSIONCOOKIE']])->retrieve();
		}

		return $this->$reqMethod($request, $response, $args);
	}

	public function renderView(ServerRequestInterface $request, ResponseInterface $response, string $template, ?array $args = []): ResponseInterface {
		$args = array_merge([
			'site_title' => $_ENV['SITE_TITLE'] ?? 'Magenta',
			'site_environment' => $_ENV['ENVIRONMENT'],
			'session' => $this->session->retrieve(),
		], $args ?? []);

		return $this->container->get('view')->render($response, $template, $args);
	}
}
