<?php
namespace MagentaServer\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DebuggingController extends Controller {
	public function requestGET(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		return $this->renderView($request, $response, "debug/index.html");
	}

	public function requestPOST(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		$action = trim($request->getParsedBody()['action']);
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
		}

		return $this->renderView($request, $response, "debug/message.html", [
			'message' => \L('debug_view_actions_error_no_action'),
		]);
	}
}
