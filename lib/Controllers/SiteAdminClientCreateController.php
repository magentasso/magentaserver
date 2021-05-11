<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\MagentaClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class SiteAdminClientCreateController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (($user = $this->session->currentUser()) === null || $user->canPerform('siteadmin') !== true) {
			throw new HttpNotFoundException($request);
		}

		return $this->renderView($request, $response, "admin/clients/create.html");
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (($user = $this->session->currentUser()) === null || $user->canPerform('siteadmin') !== true) {
			throw new HttpNotFoundException($request);
		}

		$rBody = $request->getParsedBody();
		$friendlyName = empty($friendlyName = trim($rBody['friendlyname'] ?? '')) ? null : $friendlyName;
		if ($friendlyName === null) {
			return $this->renderView($request, $response, "admin/clients/create.html", [
				'message' => \L('string_required_not_provided'),
			]);
		}
		
		$client = new MagentaClient([
			'friendly_name' => $friendlyName,
			'allowed_scopes' => ''
		]);
		$tokens = $client->generateClientTokens();
		$client->save();
		
		return $this->renderView($request, $response, "admin/clients/create.html", [
			'message' => \L('siteadmin_clientscreate_success', $tokens),
		]);
	}
}
