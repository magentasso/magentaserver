<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class SiteAdminController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (($user = $this->session->currentUser()) === null || $user->canPerform('siteadmin') !== true) {
			throw new HttpNotFoundException($request);
		}

		return $this->renderView($request, $response, "admin/index.html");
	}
}
