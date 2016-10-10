<?php namespace Cobwebinfo\ShrekApiClient\Auth\Oauth2Client;

use Cobwebinfo\ShrekApiClient\Auth\Oauth2Client\Exceptions\BasicException;
use Cobwebinfo\ShrekApiClient\Auth\Oauth2Client\Exceptions\InvalidArgumentException;
use Cobwebinfo\ShrekApiClient\Exception\IdentityProviderException;

class Oauth2Client {

	/**
	 * Different AUTH method
	 */
	const AUTH_TYPE_URI                 = 0;
	const AUTH_TYPE_AUTHORIZATION_BASIC = 1;
	const AUTH_TYPE_FORM                = 2;

	/**
	 * Different Access token type
	 */
	const ACCESS_TOKEN_URI      = 0;
	const ACCESS_TOKEN_BEARER   = 1;
	const ACCESS_TOKEN_OAUTH    = 2;
	const ACCESS_TOKEN_MAC      = 3;

	/**
	 * Different Grant types
	 */
	const GRANT_TYPE_AUTH_CODE          = 'authorization_code';
	const GRANT_TYPE_PASSWORD           = 'password';
	const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
	const GRANT_TYPE_REFRESH_TOKEN      = 'refresh_token';

	/**
	 * HTTP Methods
	 */
	const HTTP_METHOD_GET    = 'GET';
	const HTTP_METHOD_POST   = 'POST';
	const HTTP_METHOD_PUT    = 'PUT';
	const HTTP_METHOD_DELETE = 'DELETE';
	const HTTP_METHOD_HEAD   = 'HEAD';
	const HTTP_METHOD_PATCH   = 'PATCH';

	/**
	 * HTTP Form content types
	 */
	const HTTP_FORM_CONTENT_TYPE_APPLICATION = 0;
	const HTTP_FORM_CONTENT_TYPE_MULTIPART = 1;

	/**
	 * Client ID
	 *
	 * @var string
	 */
	protected $client_id = "";

	/**
	 * Client Secret
	 *
	 * @var string
	 */
	protected $client_secret = "";

	/**
	 * Client Authentication method
	 *
	 * @var int
	 */
	protected $client_auth = self::AUTH_TYPE_URI;

	/**
	 * Access Token
	 *
	 * @var string
	 */
	protected $access_token = "";

	/**
	 * Access Token Type
	 *
	 * @var int
	 */
	protected $access_token_type = self::ACCESS_TOKEN_URI;

	/**
	 * Access Token Secret
	 *
	 * @var string
	 */
	protected $access_token_secret = "";

	/**
	 * Access Token crypt algorithm
	 *
	 * @var string
	 */
	protected $access_token_algorithm = "";

	/**
	 * Access Token Parameter name
	 *
	 * @var string
	 */
	protected $access_token_param_name = 'access_token';

	/**
	 * The path to the certificate file to use for https connections
	 *
	 * @var string  Defaults to .
	 */
	protected $certificate_file = "";

	/**
	 * cURL options
	 *
	 * @var array
	 */
	protected $curl_options = array();

	/**
	 * Refresh-Token
	 *
	 * @var string
	 */
	protected $refresh_token = "";

	/**
	 * Constructor
	 *
	 * @param string $client_id Client ID
	 * @param string $client_secret Client Secret
	 * @param int    $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
	 * @param string $certificate_file Indicates if we want to use a certificate file to trust the server. Optional, defaults to null.
	 * @throws Exceptions\BasicException
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function __construct($client_id = null, $client_secret = null, $client_auth = self::AUTH_TYPE_URI, $certificate_file = null) {
		if (!extension_loaded('curl')) {
			throw new BasicException('The PHP extension curl must be installed to use this library.', BasicException::CURL_NOT_FOUND);
		}

		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->client_auth   = $client_auth;
		$this->certificate_file = $certificate_file;

		if (!empty($this->certificate_file)  && !is_file($this->certificate_file)) {
			throw new InvalidArgumentException('The certificate file was not found', InvalidArgumentException::CERTIFICATE_NOT_FOUND);
		}
	}

	/**
	 * Get the client Id
	 *
	 * @return string Client ID
	 */
	public function getClientId()
	{
		return $this->client_id;
	}

	/**
	 * Get the client Secret
	 *
	 * @return string Client Secret
	 */
	public function getClientSecret()
	{
		return $this->client_secret;
	}

	/**
	 * getAuthenticationUrl
	 *
	 * @param string $auth_endpoint Url of the authentication endpoint
	 * @param string $redirect_uri  Redirection URI
	 * @param array  $extra_parameters  Array of extra parameters like scope or state (Ex: array('scope' => null, 'state' => ''))
	 * @return string URL used for authentication
	 */
	public function getAuthenticationUrl($auth_endpoint, $redirect_uri, array $extra_parameters = array())
	{
		$parameters = array_merge(array(
			'response_type' => 'code',
			'client_id'     => $this->client_id,
			'redirect_uri'  => $redirect_uri
		), $extra_parameters);

		return $auth_endpoint . '?' . http_build_query($parameters, null, '&');
	}

    /**
     * @param $token_endpoint
     * @param $grant_type
     * @param array $parameters
     * @return array
     * @throws BasicException
     * @throws IdentityProviderException
     */
	public function fetchAccessToken($token_endpoint, $grant_type, array $parameters) {
		if (!$grant_type) {
			throw new InvalidArgumentException('The grant_type is mandatory.', InvalidArgumentException::INVALID_GRANT_TYPE);
		}

		$grantTypeClassName = $this->convertToCamelCase($grant_type);
		$grantTypeClass =  __NAMESPACE__ . '\\GrantType\\' . $grantTypeClassName;

		if (!class_exists($grantTypeClass)) {
			throw new InvalidArgumentException('Unknown grant type \'' . $grant_type . '\'', InvalidArgumentException::INVALID_GRANT_TYPE);
		}

		$grantTypeObject = new $grantTypeClass();
		$grantTypeObject->validateParameters($parameters);
		if (!defined($grantTypeClass . '::GRANT_TYPE')) {
			throw new BasicException('Unknown constant GRANT_TYPE for class ' . $grantTypeClassName, BasicException::GRANT_TYPE_ERROR);
		}

		$parameters['grant_type'] = $grantTypeClass::GRANT_TYPE;
		$http_headers = array();
		switch ($this->client_auth) {
			case self::AUTH_TYPE_URI:
			case self::AUTH_TYPE_FORM:
				$parameters['client_id'] = $this->client_id;
				$parameters['client_secret'] = $this->client_secret;
				break;
			case self::AUTH_TYPE_AUTHORIZATION_BASIC:
				$parameters['client_id'] = $this->client_id;
				$http_headers['Authorization'] = 'Basic ' . base64_encode($this->client_id .  ':' . $this->client_secret);
				break;
			default:
				throw new BasicException('Unknown client auth type.', BasicException::INVALID_CLIENT_AUTHENTICATION_TYPE);
				break;
		}

		$result = $this->executeRequest($token_endpoint, $parameters, self::HTTP_METHOD_POST, $http_headers, self::HTTP_FORM_CONTENT_TYPE_APPLICATION);

		if( isset( $result['result'] ) && !empty( $result['result']['access_token'] ) ) {
			return $result['result'];
		} else {
			throw new IdentityProviderException('Error while requesting Token-Endppoint: ' . $token_endpoint, BasicException::TOKEN_ENDPOINT_ERROR, $result['result']);
		}
	}

	/**
	 * setToken
	 *
	 * @param string $token Set the access token
	 * @return void
	 */
	public function setAccessToken($token) {

		$this->access_token = $token;

	}

	/**
	 * Set the client authentication type
	 *
	 * @param string $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
	 * @return void
	 */
	public function setClientAuthType($client_auth) {

		$this->client_auth = $client_auth;

	}

	/**
	 * Set an option for the curl transfer
	 *
	 * @param int   $option The CURLOPT_XXX option to set
	 * @param mixed $value  The value to be set on option
	 * @return void
	 */
	public function setCurlOption($option, $value) {

		$this->curl_options[$option] = $value;

	}

	/**
	 * Set multiple options for a cURL transfer
	 *
	 * @param array $options An array specifying which options to set and their values
	 * @return void
	 */
	public function setCurlOptions($options) {

		$this->curl_options = array_merge($this->curl_options, $options);

	}

	/**
	 * Set the access token type
	 *
	 * @param int $type Access token type (ACCESS_TOKEN_BEARER, ACCESS_TOKEN_MAC, ACCESS_TOKEN_URI)
	 * @param string $secret The secret key used to encrypt the MAC header
	 * @param string $algorithm Algorithm used to encrypt the signature
	 * @return void
	 */
	public function setAccessTokenType($type, $secret = null, $algorithm = null) {

		$this->access_token_type = $type;
		$this->access_token_secret = $secret;
		$this->access_token_algorithm = $algorithm;

	}

	/**
	 * Execute a request (with curl)
	 *
	 * @param        $url
	 * @param array  $parameters
	 * @param string $http_method
	 * @param array  $http_headers
	 * @param int    $form_content_type
	 *
	 * @return array
	 * @throws Exceptions\BasicException
	 */
	private function executeRequest($url, $parameters = array(), $http_method = self::HTTP_METHOD_GET, array $http_headers = null, $form_content_type = self::HTTP_FORM_CONTENT_TYPE_MULTIPART) {

		$curl_options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_CUSTOMREQUEST  => $http_method
		);

		switch($http_method) {
			case self::HTTP_METHOD_POST:
				$curl_options[CURLOPT_POST] = true;
			/* No break */
			case self::HTTP_METHOD_PUT:
			case self::HTTP_METHOD_PATCH:

				/**
				 * Passing an array to CURLOPT_POSTFIELDS will encode the data as multipart/form-data,
				 * while passing a URL-encoded string will encode the data as application/x-www-form-urlencoded.
				 * http://php.net/manual/en/function.curl-setopt.php
				 */
				if(is_array($parameters) && self::HTTP_FORM_CONTENT_TYPE_APPLICATION === $form_content_type) {
					$parameters = http_build_query($parameters, null, '&');
				}
				$curl_options[CURLOPT_POSTFIELDS] = $parameters;
				break;
			case self::HTTP_METHOD_HEAD:
				$curl_options[CURLOPT_NOBODY] = true;
			/* No break */
			case self::HTTP_METHOD_DELETE:
			case self::HTTP_METHOD_GET:
				if (is_array($parameters)) {
					$url .= '?' . http_build_query($parameters, null, '&');
				} elseif ($parameters) {
					$url .= '?' . $parameters;
				}
				break;
			default:
				break;
		}

		$curl_options[CURLOPT_URL] = $url;

		if (is_array($http_headers)) {
			$header = array();
			foreach($http_headers as $key => $parsed_urlvalue) {
				$header[] = "$key: $parsed_urlvalue";
			}
			$curl_options[CURLOPT_HTTPHEADER] = $header;
		}

		$ch = curl_init();

		curl_setopt_array($ch, $curl_options);

		// https handling
		if (!empty($this->certificate_file)) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_CAINFO, $this->certificate_file);
		} else {
			// bypass ssl verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		if (!empty($this->curl_options)) {
			curl_setopt_array($ch, $this->curl_options);
		}
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		if ($curl_error = curl_error($ch)) {
			throw new BasicException($curl_error, BasicException::CURL_ERROR);
		} else {
			$json_decode = json_decode($result, true);
		}
		curl_close($ch);

		return array(
			'result' => (null === $json_decode) ? $result : $json_decode,
			'code' => $http_code,
			'content_type' => $content_type
		);
	}

	/**
	 * Set the name of the parameter that carry the access token
	 *
	 * @param string $name Token parameter name
	 * @return void
	 */
	public function setAccessTokenParamName($name) {

		$this->access_token_param_name = $name;

	}

	/**
	 * Converts the class name to camel case
	 *
	 * @param  mixed  $grant_type  the grant type
	 * @return string
	 */
	private function convertToCamelCase($grant_type) {

		$parts = explode('_', $grant_type);
		array_walk($parts, function(&$item) {
			$item = ucfirst($item);
		});
		return implode('', $parts);

	}

    /**
     * @return null|string
     */
	protected function getRefreshToken() {
		return ( $this->refresh_token !== null ) ? $this->refresh_token : null;
	}
}