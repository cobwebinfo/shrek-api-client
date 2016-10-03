<?php namespace Cobwebinfo\ShrekApiClient\Auth;

use Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException;

/**
 * Class ClientCredentialsAuthenticator
 *
 * Fetches a token using oAuth client credentials
 * methodology.
 *
 * @package Cobwebinfo\ShrekApiClient\Auth
 */
class ClientCredentialsParameters extends OAuthParameters
{
    /**
     * ClientCredentialsParameters constructor.
     */
    public function __construct(array $config)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->urlAccessToken = $config['auth_uri'];

        if(empty($this->clientId) || empty($this->clientSecret)) {
            throw new InvalidParameterException('Client credentials grant requires both clientId and clientSecret properties.');
        }
    }
}