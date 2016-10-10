<?php namespace Cobwebinfo\ShrekApiClient\Auth\Oauth2Client\Exceptions;


class BasicException extends \Exception {

	const CURL_NOT_FOUND						= 0x01;
	const CURL_ERROR                         	= 0x02;
	const GRANT_TYPE_ERROR                   	= 0x03;
	const INVALID_CLIENT_AUTHENTICATION_TYPE 	= 0x04;
	const INVALID_ACCESS_TOKEN_TYPE          	= 0x05;
	const TOKEN_ENDPOINT_ERROR				 	= 0x06;

} 