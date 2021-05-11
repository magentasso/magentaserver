<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserSettingsController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (!$this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('auth-login'))->withStatus(302);
		}
		
		$user = $this->session->currentUser();
		return $this->renderView($request, $response, "usersettings/index.html", [
			'csrf_token' => $this->csrf->generate('csrf'),
			'user' => $user,
		]);
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if (!$this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('auth-login'))->withStatus(302);
		}

		$user = $this->session->currentUser();
		$rBody = $request->getParsedBody();

		// CSRF check
		$this->csrf->check('csrf', $rBody['_csrf'], null, true);
		
		// Get requested action
		$requestAction = empty($requestAction = trim($rBody['action'] ?? '')) ? null : $requestAction;
		if ($requestAction === 'updateDetails') {
			$email = empty($email = trim($rBody['email'] ?? '')) ? null : $email;
			$name_first = empty($name_first = trim($rBody['name_first'] ?? '')) ? null : $name_first;
			$name_last = empty($name_last = trim($rBody['name_last'] ?? '')) ? null : $name_last;
			
			if ($email === null || $name_first === null || $name_last === null) {
				return $this->renderView($request, $response, "usersettings/index.html", [
					'csrf_token' => $this->csrf->generate('csrf'),
					'user' => $user,
					'message' => \L('string_required_not_provided'),
				]);
			}

			$user->name_first = $name_first;
			$user->name_last = $name_last;
			$user->email = $email;
			$user->save();
		}
		
		return $this->renderView($request, $response, "usersettings/index.html", [
			'csrf_token' => $this->csrf->generate('csrf'),
			'user' => $user,
		]);
	}
}
