<?php

namespace Cognito\Fast;

use Exception;

/**
 * Login via the fast.co service
 *
 * @package Cognito
 * @author Josh Marshall <josh@jmarshall.com.au>
 */
class Fast {

	private $fast_app_key = NULL;
	private $secret_key = NULL;

	public function __construct($fast_app_key, $secret_key) {
		$this->fast_app_key = $fast_app_key;
		$this->secret_key = $secret_key;
	}

	/**
	 * Quick function to output the required code
	 *
	 * @param string $fast_app_key
	 * @return string
	 */
	public static function quickLoginButton($fast_app_key) {
		$fast = new self($fast_app_key, '');
		return $fast->loginButton();
	}

	/**
	 * Quick function to check a login
	 *
	 * @param string $fast_app_key
	 * @param string $secret_key
	 * @return boolean
	 */
	public static function quickCheckLogin($fast_app_key, $secret_key) {
		$fast = new self($fast_app_key, $secret_key);
		return $fast->checkResponse();
	}

	/**
	 * Get the html code to render the login button
	 */
	public function loginButton() {
		return '<fast-button></fast-button><script src="https://js.fast.co/button.js?key=' . $this->fast_app_key . '"></script>';
	}

	/**
	 * Check the login and decide whether to approve.
	 * Leave parameters blank to autodetect from $_REQUEST
	 *
	 * @param string $challengeId the challengeId field from the request.
	 * @param string $oth the oth field from the request.
	 * @param string $identifier the identifier field from the request.
	 * @return boolean
	 */
	public function checkLogin($challengeId = NULL, $oth = NULL, $identifier = NULL) {
		if (is_null($challengeId)) {
			$challengeId = isset($_REQUEST['challengeId']) ? $_REQUEST['challengeId'] : false;
		}
		if (is_null($oth)) {
			$oth = isset($_REQUEST['oth']) ? $_REQUEST['oth'] : false;
		}
		if (is_null($identifier)) {
			$identifier = isset($_REQUEST['identifier']) ? $_REQUEST['identifier'] : false;
		}

		if ($identifier && $challengeId && $oth) {
			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', 'https://api.fast.co/api/verify', [
				'query' => [
					'challengeId' => $challengeId,
					'oth' => $oth,
					'identifier' => $identifier,
					'key' => $this->fast_app_key,
					'secret' => $this->secret_key,
				],
			]);
			$decoded = json_decode($res->getBody());

			if ($decoded && property_exists($decoded, 'success') && $decoded->success) {
				return true;
			} else {
				if ($decoded && property_exists($decoded, 'error')) {
					throw new Exception($decoded->error);
				}
			}
		}
		return false;
	}
}
