<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthLogoutController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (!$this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('auth-login'))->withStatus(302);
		}

		return $this->renderView($request, $response, "auth/logout.html");
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (!$this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('auth-login'))->withStatus(302);
		}

		$this->csrf->check('csrf', $request->getParsedBody()['_csrf'], null, true);
		$this->session->destroy();
		
		return $response->withHeader('Location', $this->urlFor('auth-login', [], ['from' => 'logout']))->withStatus(302);
	}
}
