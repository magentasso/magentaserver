<?php
namespace MagentaServer\Models;
use Base32\Base32;

class MagentaClient extends Model {
	protected $fillable = [
		'friendly_name',
		'allowed_scopes',
	];

	/**
	 * Generate new client ID and secret values for this model instance, if either
	 * of those two values are `null`. Does not automatically save the model.
	 *
	 * @return string[] Array of [$client_id, $client_secret]
	 */
	public function generateClientTokens(): array {
		if ($this->client_id === null) {
			$this->client_id = Base32::encode(random_bytes(20));
		}

		if ($this->client_secret === null) {
			$this->client_secret = Base32::encode(random_bytes(40));
		}

		return [$this->client_id, $this->client_secret];
	}
	
	/**
	 * Get the list of allowed scopes for this client.
	 *
	 * @return string[] Allowed scopes
	 */
	public function getAllowedScopes(): array {
		return array_map('trim', array_filter(explode(",", $this->allowed_scopes)));
	}

	/**
	 * Create a generic MagentaResponse for this client.
	 *
	 * @return \MagentaSSO\MagentaResponse The new MagentaResponse
	 */
	public function createResponse(): \MagentaSSO\MagentaResponse {
		return new \MagentaSSO\MagentaResponse(
			$this->client_id,
			$this->client_secret,
			null, 
			[],
			[],
		);
	}
}

