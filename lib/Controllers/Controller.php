<?php
namespace MagentaServer\Controllers;

use MagentaServer\Session\Session;
use MagentaServer\Session\EasyCSRFSessionProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller {
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		$reqMethod = "request{$request->getMethod()}";
		if (!method_exists($this, $reqMethod)) {
			return $response->withStatus(405);
		}

		return $this->$reqMethod($request, $response, $args);
	}

	public function session(ServerRequestInterface $request): Session {
		$session_id = null;

		$cookies = $request->getCookieParams();
		if (array_key_exists($_ENV['SITE_SESSIONCOOKIE'], $cookies)) {
			$session_id = $cookies[$_ENV['SITE_SESSIONCOOKIE']];
		}

		return new Session($session_id);
	}

	public function csrf(ServerRequestInterface $request): \EasyCSRF\EasyCSRF {
		$session = $this->session($request);
		$sessionProvider = new EasyCSRFSessionProvider($session);
        return new \EasyCSRF\EasyCSRF($sessionProvider);
	}

	public function renderView(ServerRequestInterface $request, ResponseInterface $response, string $template, ?array $args = []): ResponseInterface {
		$args = array_merge([
			'site_title' => $_ENV['SITE_TITLE'] ?? 'Magenta',
			'session' => $this->session($request)->retrieve(),
		], $args ?? []);

		return $this->container->get('view')->render($response, $template, $args);
	}
}
