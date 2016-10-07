<?php namespace Cobwebinfo\ShrekApiClient;

use Cobwebinfo\ShrekApiClient\Auth\Oauth2Client\Oauth2Client;
use Cobwebinfo\ShrekApiClient\Auth\AccessToken;
use Cobwebinfo\ShrekApiClient\Auth\ClientCredentialsParameters;
use Cobwebinfo\ShrekApiClient\Support\HttpRequester;

/**
 * Class DefaultApiConnector
 *
 * @package Cobwebinfo\ShrekApiClient
 */
class DefaultApiConnector implements ApiConnector
{
    /**
     * @var HttpRequester
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $config;

    /**
     * DefaultApiConnector constructor.
     * @param HttpRequester $httpClient
     */
    public function __construct(array $config, HttpRequester $httpClient)
    {
        $this->config = $config;

        $this->httpClient = $httpClient;
    }

    /**
     * @return Auth\AccessToken
     */
    public function fetchAccessToken()
    {
        $authParams = new ClientCredentialsParameters($this->config);

        $authParams = $authParams->toArray();

        $client = new Oauth2Client($authParams['clientId'], $authParams['clientSecret']);

        $accessToken = $client->fetchAccessToken($authParams['urlAccessToken'], 'client_credentials', $authParams);

        return new AccessToken($accessToken);
    }

    /**
     * @return mixed
     */
    public function httpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function clientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function clientSecret()
    {
        return $this->clientSecret;
    }
}