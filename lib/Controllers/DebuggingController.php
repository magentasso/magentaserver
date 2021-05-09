<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DebuggingController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		return $this->renderView($request, $response, "debug/index.html");
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		$requestBody = $request->getParsedBody();
		$action = trim($requestBody['action']);
		if ($action === 'destroySession') {
			$old_session_id = $this->session->session_id;
			$this->session->destroy();

			return $this->renderView($request, $response, "debug/message.html", [
				'message' => \L('debug_view_session_destroy_success', [$old_session_id ?? 'null']),
			]);

		} elseif ($action === 'populateSession') {
			$this->session->ensureCreate()->update();
			$this->csrf->generate('csrf');

			return $this->renderView($request, $response, "debug/message.html", [
				'message' => \L('debug_view_session_populate_success', [$this->session->session_id ?? 'null']),
			]);

		} elseif ($action === 'createUser') {
			$this->csrf->check('csrf', $requestBody['_csrf'], null, true);

			// Get parameters
			$email = empty($email = trim($requestBody['email'] ?? '')) ? null : $email;
			$perms = empty($perms = trim($requestBody['perms'] ?? '')) ? '' : $perms;
			if ($email === null) {
				return $this->renderView($request, $response, "debug/message.html", [
					'message' => \L('debug_view_createuser_error_no_email'),
				]);
			}

			// Hash password
			$password = 'password';
			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			$user = User::create([
				'email' => $email,
				'password_hash' => $password_hash,
				'name_first' => 'Test',
				'name_last' => 'User',
				'permissions' => $perms,
			]);

			if ($user === null) {
				return $this->renderView($request, $response, "debug/message.html", [
					'message' => \L('debug_view_createuser_error_create_failed'),
				]);
			}

			$user->save();

			return $this->renderView($request, $response, "debug/message.html", [
				'message' => \L('debug_view_createuser_success', [$user->id, $user->email, $password]),
			]);
		}

		return $this->renderView($request, $response, "debug/message.html", [
			'message' => \L('debug_view_actions_error_no_action'),
		]);
	}
}
