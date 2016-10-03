<?php namespace Cobwebinfo\ShrekApiClient;

use Cobwebinfo\ShrekApiClient\Auth\ClientCredentialsParameters;
use Cobwebinfo\ShrekApiClient\Support\HttpRequester;
use GuzzleHttp\ClientInterface;
use \League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Class DefaultApiConnector
 *
 * @package Cobwebinfo\ShrekApiClient
 */
class DefaultApiConnector implements ApiConnector
{
    /**
     * @var ClientInterface
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
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function fetchAccessToken()
    {
        $authParams = new ClientCredentialsParameters($this->config);

        $provider = new GenericProvider($authParams->toArray());

        $accessToken = $provider->getAccessToken('client_credentials');

        return $accessToken;
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