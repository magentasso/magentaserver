<?php
namespace MagentaServer\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use MagentaSSO\MagentaRequest;
use MagentaSSO\MagentaResponse;
use MagentaSSO\MagentaSignatureException;
use MagentaServer\Models\MagentaClient;

class SSOMagentaController extends Controller {
	public function requestGET(Request $request, Response $response, array $args): Response {
		$queryParams = ((array) $request->getQueryParams());

		// Get our client ID, payload, and signature
		$clientID = array_key_exists('client', $queryParams) ? $queryParams['client'] : null;
		$clientPayload = array_key_exists('payload', $queryParams) ? $queryParams['payload'] : null;
		$clientSignature = array_key_exists('signature', $queryParams) ? $queryParams['signature'] : null;

		// Throw a BadRequest if any of the above fields are `null`
		if ($clientID === null || $clientPayload === null || $clientSignature === null) {
			throw new HttpBadRequestException($request);
		}

		// Check if we are logged in - if we aren't, redirect to the login form
		// The login form will redirect back here after a successful login, with
		// our request parameters (client/payload/signature) intact.
		if (($currentUser = $this->session->currentUser()) === null) {
			$thisUri = strval($request->getUri());
			$nextUri = $this->urlFor('auth-login', [], ['next' => $thisUri]);
			return $response->withHeader('Location', $nextUri)->withStatus(302);
		}

		// Retrieve the model instance for the given client ID
		$clientModel = MagentaClient::where('client_id', '=', $clientID)->first();
		if ($clientModel === null) {
			throw new HttpBadRequestException($request);
		}

		// Decode the MagentaRequest, if we get a signature error rethrow a BadRequest
		$magentaRequest = new MagentaRequest($clientModel->client_id, $clientModel->client_secret);
		try { $magentaRequest->decode($clientPayload, $clientSignature); }
		catch (MagentaSignatureException $e) { throw new HttpBadRequestException($request); }
		
		// Construct the new MagentaResponse
		$magentaResponse = $clientModel->createResponse();
		$magentaResponse->data['nonce'] = $magentaRequest->data['nonce'];
		$magentaResponse->data['user_data'] = [
			'external_id' => $currentUser->id,
			'email' => $currentUser->email,
		];
		
		// Get the intersection of the allowed scopes and the requested scopes
		$requestedScopes = array_intersect($clientModel->getAllowedScopes(), $magentaRequest->data['scopes']);
		
		// Fill in the "profile" scope, if requested
		if (in_array('profile', $requestedScopes)) {
			$magentaResponse->data['scope_data']['profile'] = [
				'name_first' => $currentUser->name_first,
				'name_last' => $currentUser->name_last,
			];
		}

		// Construct the callbackUri by casting the $magentaResponse to a string,
		// resulting in the encoded query values
		$callbackUri = ((string) $magentaRequest->data['callback_url']) . '?' . strval($magentaResponse);

		// Okay, we're done! Redirect to the request's callback_url
		return $response->withHeader('Location', $callbackUri)->withStatus(302);
	}
}
