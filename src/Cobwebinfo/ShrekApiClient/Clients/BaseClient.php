<?php namespace Cobwebinfo\ShrekApiClient\Clients;

use Cobwebinfo\ShrekApiClient\ApiConnector;
use Cobwebinfo\ShrekApiClient\Cache\Contracts\Store;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    /**
     * @var ApiConnector
     */
    protected $connector;

    /**
     * @var Store
     */
    protected $store;

    /**
     * BaseClient constructor.
     * @param ApiConnector $connector
     * @param Store $store
     */
    public function __construct(ApiConnector $connector, Store $store)
    {
        $this->connector = $connector;
        $this->store = $store;
    }

    /**
     * Sends get request.
     *
     * @param $resource
     * @param array $headers
     * @param array $query
     * 
     * @return mixed
     */
    public function get($resource, array $headers = [], array $query = [])
    {
        $accessToken = $this->fetchAccessToken();

        $headers['Authorization'] = 'Bearer ' . $accessToken;

        $response = $this->connector->httpClient()->get($resource, [
            'headers' => $headers,
            'query' => $query
        ]);

        return $response;
    }

    /**
     * @param $response
     * @return mixed
     */
    public function parseSuccess(ResponseInterface $response)
    {
        $payload = null;

        $json = \json_decode($response->getBody(), true);

        if($json && isset($json['data'])) {
            $payload = $json['data'];
        }

        return $payload;
    }

    /**
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    public function fetchAccessToken()
    {
        $token = $this->fetchAccessTokenFromCache();

        if(!$token) {
            $token = $this->connector->fetchAccessToken();

            $this->store->put('access_token', $token, 60);
        }

        return $token;
    }

    /**
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    protected function fetchAccessTokenFromCache()
    {
        $accessToken = $this->store->get('access_token');

        if(!$accessToken || $accessToken->hasExpired()) {
            return false;
        }

        return $accessToken;
    }
}