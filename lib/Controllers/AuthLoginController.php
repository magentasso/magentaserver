<?php
namespace MagentaServer\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthLoginController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		return $this->renderView($request, $response, "auth/login.html");
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		$this->csrf->check('csrf', $request->getParsedBody()['_csrf'], null, true);
		return $this->renderView($request, $response, "auth/login.html", [
			'message' => 'Not implemented!',
		]);
	}
}
