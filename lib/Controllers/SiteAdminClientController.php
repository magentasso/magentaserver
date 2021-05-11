<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\MagentaClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class SiteAdminClientController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (($user = $this->session->currentUser()) === null || $user->canPerform('siteadmin') !== true) {
			throw new HttpNotFoundException($request);
		}

		$clients = [];
		foreach (MagentaClient::get() as $client) {
			$clients[] = [
				'friendly_name' => $client->friendly_name,
				'client_id' => $client->client_id,
				'updated' => $client->updated,
			];
		}

		return $this->renderView($request, $response, "admin/clients/index.html", [
			'clients' => $clients,
		]);
	}
}
