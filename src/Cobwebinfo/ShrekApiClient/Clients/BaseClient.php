<?php namespace Cobwebinfo\ShrekApiClient\Clients;

use Cobwebinfo\ShrekApiClient\ApiConnector;
use Cobwebinfo\ShrekApiClient\Cache\Contracts\Store;
use Cobwebinfo\ShrekApiClient\Cache\KeyGenerator;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    /**
     * Class which connects to API.
     *
     * @var ApiConnector
     */
    protected $connector;

    /**
     * Class which handles caching.
     *
     * @var Store
     */
    protected $store;

    /**
     * @var KeyGenerator
     */
    protected $keyGenerator;

    /**
     * BaseClient constructor.
     *
     * @param ApiConnector $connector
     * @param Store $store
     */
    public function __construct(ApiConnector $connector, Store $store)
    {
        $this->connector = $connector;
        $this->store = $store;
        $this->keyGenerator = new KeyGenerator();
    }

    /**
     * Sends get request.
     *
     * @param $resource
     * @param array $headers
     * @param array $query
     *
     * @return ResponseInterface
     */
    public function get($resource, array $headers = [], array $query = [])
    {
        $accessToken = $this->fetchAccessToken();

        $cached = $this->fromCache($resource, $query, $accessToken);

        if ($cached) {
            $response = $cached;
        } else {
            $headers['Authorization'] = 'Bearer ' . $accessToken;

            $response = $this->connector->httpClient()->get($resource, [
                'headers' => $headers,
                'query' => $query
            ]);

            $this->toCache($this->getCacheKey($resource, $query, $accessToken), $response);
        }

        return $response;
    }

    /**
     * Fetches response from cache if available.
     *
     * @param $resource
     * @param array $query
     * @return mixed
     */
    protected function fromCache($resource, array $query, $accessToken)
    {
        $key = $this->getCacheKey($resource, $query, $accessToken);

        $cached = $this->store->get($key);

        return $cached;
    }

    /**
     * Adds response to cache,
     *
     * @param $key
     * @param $response
     */
    protected function toCache($key, $response)
    {
        $this->store->put($key, $response, 30);
    }

    /**
     * @param $resource
     * @param array $query
     * @param $accessToken
     * @return string
     */
    protected function getCacheKey($resource, array $query, $accessToken)
    {
        return $this->keyGenerator->generate(
            $resource,
            $query,
            $accessToken
        );
    }

    /**
     * Parses a ResponseInterface object.
     *
     * @param $response
     *
     * @return null|array
     */
    public function parseSuccess(ResponseInterface $response)
    {
        $payload = null;

        $json = \json_decode($response->getBody(), true);

        if ($json && isset($json['data'])) {
            $payload = $json['data'];
        }

        return $payload;
    }

    /**
     * Fetches access token from cache if one is
     * available, or from API if not.
     *
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    public function fetchAccessToken()
    {
        $token = $this->fetchAccessTokenFromCache();

        if (!$token) {
            $token = $this->connector->fetchAccessToken();

            $this->store->put('access_token', $token, 60);
        }

        return $token;
    }

    /**
     * Fetches access token from cache.
     *
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    protected function fetchAccessTokenFromCache()
    {
        $accessToken = $this->store->get('access_token');

        if (!$accessToken || $accessToken->hasExpired()) {
            return false;
        }

        return $accessToken;
    }
}