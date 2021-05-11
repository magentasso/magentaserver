<?php
namespace MagentaServer\Controllers;

use MagentaServer\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthLoginController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if ($this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('index'))->withStatus(302);
		}
		
		$queryParams = $request->getQueryParams();
		$message = null;
		if (array_key_exists('from', $queryParams) && $queryParams['from'] == 'logout') {
			$message = \L('auth_logout_success');
		} elseif (array_key_exists('next', $queryParams)) {
			$message = \L('auth_login_creds_to_continue');
		}

		return $this->renderView($request, $response, "auth/login.html", [
			'message' => $message,
		]);
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		if ($this->session->isLoggedIn()) {
			return $response->withHeader('Location', $this->urlFor('index'))->withStatus(302);
		}

		$queryParams = $request->getQueryParams();
		$requestBody = $request->getParsedBody();
		$this->csrf->check('csrf', $requestBody['_csrf'], null, true);
		
		// Get parameters
		$email = empty($email = trim($requestBody['email'] ?? '')) ? null : $email;
		$password = empty($password = trim($requestBody['password'] ?? '')) ? null : $password;
		if ($email === null || $password === null) {
			return $this->renderView($request, $response, "auth/login.html", [
				'message' => \L('auth_login_error_invalid_creds'),
			]);
		}
		
		// Get user object
		$user = User::where('email', '=', $email)->first();
		if ($user === null) {
			return $this->renderView($request, $response, "auth/login.html", [
				'message' => \L('auth_login_error_invalid_creds'),
			]);
		}

		// Check password
		if (password_verify($password, $user->password_hash) === false) {
			return $this->renderView($request, $response, "auth/login.html", [
				'message' => \L('auth_login_error_invalid_creds'),
			]);
		}
		
		// If we get here, login is successful
		$this->session->session_data['userid'] = $user->id;
		$this->session->ensureCreate()->update();
		
		// Redirect!
		$nextUri = $this->urlFor('index');
		if (array_key_exists('next', $queryParams)) {
			$nextUri = $queryParams['next'];
		}

		return $response->withHeader('Location', $nextUri)->withStatus(302);
	}
}
