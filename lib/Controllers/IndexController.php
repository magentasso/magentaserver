<?php
namespace MagentaServer\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		return $this->renderView($request, $response, "index.html");
	}
}
