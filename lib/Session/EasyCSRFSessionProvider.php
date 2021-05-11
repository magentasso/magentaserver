<?php
namespace MagentaServer\Session;

use MagentaServer\Session\Session;
use EasyCSRF\Interfaces\SessionProvider;

class EasyCSRFSessionProvider implements SessionProvider {
	/** @var Session $session */

	protected $session;
	public function __construct(Session &$session) {
		$this->session = $session;
	}

	public function get($key) {
		$this->session->retrieve();
		if (!array_key_exists($key, $this->session->session_data)) {
			return null;
		}

		return $this->session->session_data[$key];
	}

	public function set($key, $value) {
		$this->session->ensureCreate()->retrieve();
		$this->session->session_data[$key] = $value;
		$this->session->update();
	}
}
