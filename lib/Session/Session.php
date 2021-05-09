<?php
namespace MagentaServer\Session;

use Base32\Base32;
use MagentaServer\Helpers\RedisInstance;

class Session {
	public ?string $session_id;
	public array $session_data;

	public function __construct(?string $session_id) {
		$this->setSessionID($session_id);
		$this->session_data = [];
	}

	public function setSessionID(?string $session_id): self {
		if (empty($this->session_id = trim(strval($session_id ?? '')))) {
			$this->session_id = null;
		}

		return $this;
	}

	public function retrieve(): self {
		if ($this->session_id !== null) {
			$session_key = self::sessionIdToRedisKey($this->session_id);
			$redis = RedisInstance::get();

			if (0 < $redis->exists($session_key)) {
				$this->session_data = $redis->hGetAll($session_key);
			}
		}

		return $this;
	}

	public function update(): self {
		if ($this->session_id !== null) {
			$session_expiry = intval(time() + (60 * 60 * 24 * 30)); // Expire session in 30 days
			$session_key = self::sessionIdToRedisKey($this->session_id);
			$redis = RedisInstance::get();

			// Delete existing session data from Redis and repopulate
			$redis->del($session_key);
			foreach ($this->session_data as $key => $value) {
				$redis->hSet($session_key, $key, strval($value));
			}

			$redis->expireAt($session_key, $session_expiry);

			// Update our cookie
			setcookie($_ENV['SITE_SESSIONCOOKIE'], $this->session_id, [
				'expires' => $session_expiry,
				'path' => '/',
				'httponly' => true,
			]);
		}

		return $this;
	}
	
	public function destroy(): void {
		if ($this->session_id !== null) {
			// Purge from Redis
			$redis = RedisInstance::get();
			$redis->del(self::sessionIdToRedisKey($this->session_id));

			// Remove the cookie by blanking it and setting it to expire 24h ago
			setcookie($_ENV['SITE_SESSIONCOOKIE'], '', [
				'expires' => time() - (60 * 60 * 24),
				'path' => '/',
				'httponly' => true,
			]);

			// And reset this Session instance
			$this->session_id = null;
			$this->session_data = [];
		}
	}

	public function ensureCreate(): self {
		if ($this->session_id === null) {
			$this->session_id = self::sessionIdGenerate();
		}

		return $this;
	}

	// User helpers

	public function currentUser() {
		return null;
	}
	
	public function isLoggedIn(): bool {
		return $this->currentUser() !== null;
	}

	// Session ID helpers

	public static function sessionIdToRedisKey(string $session_id): string {
		return "msSession:{$session_id}";
	}

	public static function sessionIdGenerate(): string {
		return strtolower(Base32::encode(random_bytes(20)));
	}
}
